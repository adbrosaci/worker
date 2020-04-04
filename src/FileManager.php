<?php declare(strict_types = 1);

namespace MartenB\Worker;

use Nette\SmartObject;

/**
 * @property string $appDir
 */
class FileManager
{

	use SmartObject;

	/** @var string */
	protected $appDir;

	public function __construct(string $appDir)
	{
		$this->appDir = $appDir;
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
