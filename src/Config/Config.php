<?php declare(strict_types = 1);

namespace Adbros\Worker\Config;

use Nette\SmartObject;

/**
 * @property mixed[] $options
 */
class Config
{

	use SmartObject;

	/** @var mixed[] */
	protected $options;

	/**
	 * @param mixed[] $options
	 */
	public function __construct(array $options)
	{
		$this->options = $options;
	}

	/**
	 * @return mixed[]
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @param mixed[] $options
	 */
	public function setOptions(array $options): void
	{
		$this->options = $options;
	}

}
