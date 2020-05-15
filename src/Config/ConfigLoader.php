<?php declare(strict_types = 1);

namespace Adbros\Worker\Config;

use Nette\Neon\Neon;
use Nette\Utils\Finder;
use Nette\Utils\Json;
use RuntimeException;
use SplFileInfo;

class ConfigLoader
{

	/**
	 * @return string[]
	 */
	public static function getConfiguration(string $directory): array
	{
		$configFilePath = self::locateConfigFile($directory);

		if ($configFilePath === null) {
			return [];
		}

		$extension = pathinfo($configFilePath, PATHINFO_EXTENSION);

		switch (strtolower($extension)) {
			case 'php':
				return self::fromPhp($configFilePath);
			case 'json':
				return self::fromJson($configFilePath);
			default:
				return self::fromNeon($configFilePath);
		}
	}

	protected static function locateConfigFile(string $directory): ?string
	{
		$configFiles = Finder::findFiles('worker.php', 'worker.json', 'worker.neon')->in($directory);

		foreach ($configFiles as $configFile) {
			/** @var SplFileInfo $configFile */
			return $configFile->getPathname();
		}

		return null;
	}

	/**
	 * @return string[]
	 */
	protected static function fromPhp(string $configFilePath): array
	{
		ob_start();
		$configArray = include $configFilePath;
		ob_end_clean();

		if (!is_array($configArray)) {
			throw new RuntimeException(sprintf(
				'PHP file \'%s\' must return an array',
				$configFilePath
			));
		}

		return $configArray;
	}

	/**
	 * @return string[]
	 */
	protected static function fromJson(string $configFilePath): array
	{
		$configArray = Json::decode(file_get_contents($configFilePath), Json::FORCE_ARRAY);

		if (!is_array($configArray)) {
			throw new RuntimeException(sprintf(
				'JSON file \'%s\' must return an array',
				$configFilePath
			));
		}

		return $configArray;
	}

	/**
	 * @return string[]
	 */
	protected static function fromNeon(string $configFilePath): array
	{
		$configArray = Neon::decode(file_get_contents($configFilePath));

		if (!is_array($configArray)) {
			throw new RuntimeException(sprintf(
				'NEON file \'%s\' must return an array',
				$configFilePath
			));
		}

		return $configArray;
	}

}
