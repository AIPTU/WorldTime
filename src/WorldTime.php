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
use pocketmine\world\World;
use function rename;
use function str_replace;

final class WorldTime extends PluginBase
{
	private const CONFIG_VERSION = 1.1;

	public function onEnable(): void
	{
		$this->checkConfig();

		$this->loadWorld();
	}

	public function replaceVars(string $str, array $vars): string
	{
		foreach ($vars as $key => $value) {
			$str = str_replace('{' . $key . '}', (string) $value, $str);
		}
		return $str;
	}

	private function checkConfig(): void
	{
		$this->saveDefaultConfig();

		if (!$this->getConfig()->exists('config-version') || ($this->getConfig()->get('config-version', self::CONFIG_VERSION) !== self::CONFIG_VERSION)) {
			$this->getLogger()->warning('An outdated config was provided attempting to generate a new one...');
			if (!rename($this->getDataFolder() . 'config.yml', $this->getDataFolder() . 'config.old.yml')) {
				$this->getLogger()->critical('An unknown error occurred while attempting to generate the new config');
				$this->getServer()->getPluginManager()->disablePlugin($this);
			}
			$this->reloadConfig();
		}
	}

	private function loadWorld(): void
	{
		foreach ($this->getConfig()->get('worlds', []) as $worlds => $value) {
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
				$message = $this->getConfig()->get('message', 'Set the time of the {WORLD} world to {TIME}');
				$this->getLogger()->notice($this->replaceVars($message, [
					'WORLD' => $world->getFolderName(),
					'TIME' => $time,
				]));
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
