<?php declare(strict_types = 1);

namespace Adbros\Worker\Tests\Jobs;

use Tester\TestCase;

abstract class JobsTestCase extends TestCase
{

	/** @var string[] */
	protected $inputs;

	/**
	 * @param string[] $inputs
	 * @return resource
	 */
	protected function createStream(array $inputs)
	{
		$stream = fopen('php://memory', 'r+', false);

		foreach ($inputs as $input) {
			fwrite($stream, $input . PHP_EOL);
		}

		rewind($stream);

		return $stream;
	}

}
