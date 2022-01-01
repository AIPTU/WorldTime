<?php

/*
 *
 * Copyright (c) 2021 AIPTU
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

declare(strict_types=1);

namespace aiptu\worldtime;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use function rename;
use function str_replace;

final class WorldTime extends PluginBase
{
	private const CONFIG_VERSION = 1.0;

	private ConfigProperty $configProperty;

	public function onEnable(): void
	{
		$this->checkConfig();

		$this->checkWorld();
	}

	public function replaceVars(string $str, array $vars): string
	{
		foreach ($vars as $key => $value) {
			$str = str_replace('{' . $key . '}', (string) $value, $str);
		}
		return $str;
	}

	public function getConfigProperty(): ConfigProperty
	{
		return $this->configProperty;
	}

	private function checkConfig(): void
	{
		$this->saveDefaultConfig();

		if (!$this->getConfig()->exists('config-version') || ($this->getConfig()->get('config-version', self::CONFIG_VERSION) !== self::CONFIG_VERSION)) {
			$this->getLogger()->notice('Your configuration file is outdated, updating the config.yml...');
			$this->getLogger()->notice('The old configuration file can be found at config.old.yml');

			rename($this->getDataFolder() . 'config.yml', $this->getDataFolder() . 'config.old.yml');

			$this->reloadConfig();
		}

		$this->configProperty = new ConfigProperty($this->getConfig());
	}

	private function checkWorld(): void
	{
		foreach ($this->getConfigProperty()->getPropertyArray('worlds', []) as $worlds => $value) {
			$this->getServer()->getWorldManager()->loadWorld($worlds);

			$world = $this->getServer()->getWorldManager()->getWorldByName($worlds);
			$time = $value['time'];
			$stop = $value['stop'];
			if ($world !== null) {
				match ($time) {
					'day' => $time = World::TIME_DAY,
					'noon' => $time = World::TIME_NOON,
					'sunset' => $time = World::TIME_SUNSET,
					'night' => $time = World::TIME_NIGHT,
					'midnight' => $time = World::TIME_MIDNIGHT,
					'sunrise' => $time = World::TIME_SUNRISE,
					default => $time = $this->getInteger($time, 0),
				};
				$world->setTime($time);
				if ($stop) {
					$world->stopTime();
				}
				$message = $this->getConfigProperty()->getPropertyString('message', 'Set the time of the {WORLD} world to {TIME}');
				$this->getLogger()->notice(TextFormat::colorize($this->replaceVars($message, [
					'WORLD' => $world->getFolderName(),
					'TIME' => $time,
				])));
			}
		}
	}

	private function getInteger(string $value, int $min = 30000000, int $max = -30000000): int
	{
		$i = (int) $value;

		if ($i < $min) {
			$i = $min;
		} elseif ($i > $max) {
			$i = $max;
		}

		return $i;
	}
}
