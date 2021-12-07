<?php

declare(strict_types=1);

namespace aiptu\worldtime;

use pocketmine\lang\KnownTranslationFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use function gettype;
use function rename;

final class WorldTime extends PluginBase
{
	public function onEnable(): void
	{
		$this->checkConfig();

		$this->checkWorld();
	}

	private function checkConfig(): void
	{
		$this->saveDefaultConfig();

		if ($this->getConfig()->get('config-version', 1) !== 1) {
			$this->getLogger()->notice('Your configuration file is outdated, updating the config.yml...');
			$this->getLogger()->notice('The old configuration file can be found at config.old.yml');

			rename($this->getDataFolder() . 'config.yml', $this->getDataFolder() . 'config.old.yml');

			$this->reloadConfig();
		}

		foreach ([
			'worlds' => 'array',
		] as $option => $expectedType) {
			if (($type = gettype($this->getConfig()->getNested($option))) !== $expectedType) {
				throw new \TypeError("Config error: Option ({$option}) must be of type {$expectedType}, {$type} was given");
			}
		}
	}

	private function checkWorld(): void
	{
		foreach ($this->getConfig()->getAll()['worlds'] as $worlds => $value) {
			$this->getServer()->getWorldManager()->loadWorld($worlds);

			$world = $this->getServer()->getWorldManager()->getWorldByName($worlds);
			$time = $value['time'];
			$stop = (bool) $value['stop'];
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
				$this->getLogger()->info($this->getServer()->getLanguage()->translate(KnownTranslationFactory::commands_time_set((string) $time)));
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
