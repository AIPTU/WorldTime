<?php

/*
 * Copyright (c) 2021-2023 AIPTU
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/AIPTU/WorldTime
 */

declare(strict_types=1);

namespace aiptu\worldtime;

use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use function assert;
use function is_int;
use function is_string;
use function max;
use function min;
use function rename;
use function str_replace;

class WorldTime extends PluginBase {
	private const CONFIG_VERSION = 1.2;

	public function onEnable() : void {
		$this->checkConfig();
		$this->loadWorlds();
	}

	/**
	 * Checks and handles the plugin's configuration.
	 */
	private function checkConfig() : void {
		$this->saveDefaultConfig();

		$config = $this->getConfig();
		if (!$config->exists('config-version') || $config->get('config-version') !== self::CONFIG_VERSION) {
			$this->getLogger()->warning('An outdated config was provided, attempting to generate a new one...');

			$configFile = $this->getDataFolder() . 'config.yml';
			$newConfigFile = $this->getDataFolder() . 'config.old.yml';

			if (!rename($configFile, $newConfigFile)) {
				$this->getLogger()->critical('An unknown error occurred while attempting to generate the new config');
				$this->getServer()->getPluginManager()->disablePlugin($this);
				return;
			}

			$this->reloadConfig();
		}
	}

	/**
	 * Loads worlds and sets their time based on the plugin's configuration.
	 */
	private function loadWorlds() : void {
		/** @var array<string, array{time: int|string, stop: bool}> $worldsConfig */
		$worldsConfig = $this->getConfig()->get('worlds', []);
		$worldManager = $this->getServer()->getWorldManager();

		foreach ($worldsConfig as $worldName => $worldData) {
			if (!$worldManager->isWorldLoaded($worldName)) {
				$worldManager->loadWorld($worldName);
			}

			$world = $worldManager->getWorldByName($worldName);
			if ($world === null) {
				$this->getLogger()->warning("Failed to load world '{$worldName}'.");
				continue;
			}

			$time = $this->getTimeValue($worldData['time']);
			$stopTime = $worldData['stop'];

			$world->setTime($time);
			if ($stopTime) {
				$world->stopTime();
			}

			$message = $this->getConfig()->get('message', 'Set the time of the {WORLD} world to {TIME}');
			assert(is_string($message), 'Invalid message format.');
			$replacements = [
				'WORLD' => $world->getFolderName(),
				'TIME' => $time,
			];
			$this->getLogger()->notice($this->replaceVars($message, $replacements));
		}
	}

	/**
	 * Converts a time string or integer to a valid World time value.
	 *
	 * @param mixed $time the time value to convert
	 *
	 * @return int the World time value
	 */
	private function getTimeValue($time) : int {
		return match ($time) {
			'day' => World::TIME_DAY,
			'noon' => World::TIME_NOON,
			'sunset' => World::TIME_SUNSET,
			'night' => World::TIME_NIGHT,
			'midnight' => World::TIME_MIDNIGHT,
			'sunrise' => World::TIME_SUNRISE,
			default => is_int($time) ? $this->getBoundedInteger($time, 0) : 0,
		};
	}

	/**
	 * Ensures the given integer value falls within the specified bounds.
	 *
	 * @param int $value the integer value to check
	 * @param int $min   the minimum allowed value
	 * @param int $max   the maximum allowed value
	 *
	 * @return int the bounded integer value
	 */
	private function getBoundedInteger(int $value, int $min, int $max = -30000000) : int {
		return max(min($value, $min), $max);
	}

	/**
	 * Replaces variables in a string with corresponding values from an array.
	 *
	 * @param string $str  the input string
	 * @param array  $vars an associative array of variables and their values
	 *
	 * @return string the modified string
	 */
	private function replaceVars(string $str, array $vars) : string {
		foreach ($vars as $key => $value) {
			$str = str_replace('{' . $key . '}', (string) $value, $str);
		}

		return $str;
	}
}
