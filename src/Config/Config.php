<?php declare(strict_types = 1);

namespace Adbros\Worker\Config;

use Nette\SmartObject;

/**
 * @property string $appDir
 */
class Config
{

	use SmartObject;

	/** @var string */
	protected $appDir;

	/**
	 * @param string[] $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->parseConfiguration($configuration);
	}

	/**
	 * @param string[] $configuration
	 */
	protected function parseConfiguration(array $configuration): void
	{
		$this->appDir = $configuration['appDir'] ?? '';
	}

	public function getAppDir(): string
	{
		return $this->appDir;
	}

	public function setAppDir(string $appDir): void
	{
		$this->appDir = $appDir;
	}

}
