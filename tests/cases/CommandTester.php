<?php declare(strict_types = 1);

namespace Adbros\Worker\Tests;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandTester
{

	private Command $command;

	public function __construct(Command $command)
	{
		$this->command = $command;
	}

	/**
	 * @param string[] $inputs
	 */
	public function run(array $inputs, bool $interactive): int
	{
		$input = new ArrayInput($interactive ? [] : $inputs);

		if ($interactive) {
			$input->setStream($this->createStream($inputs));
		}

		$output = new BufferedOutput();

		return $this->command->run($input, $output);
	}

	/**
	 * @param string[] $inputs
	 * @return resource
	 */
	private function createStream(array $inputs)
	{
		$stream = fopen('php://memory', 'r+', false);

		foreach ($inputs as $input) {
			fwrite($stream, $input . PHP_EOL);
		}

		rewind($stream);

		return $stream;
	}

}
