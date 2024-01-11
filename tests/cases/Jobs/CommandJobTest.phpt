<?php declare(strict_types = 1);

namespace Adbros\Worker\Tests\Jobs;

use Adbros\Worker\Config\Config;
use Adbros\Worker\Console\Command\WorkerCommand;
use Adbros\Worker\Job\CommandJob;
use Adbros\Worker\Tests\CommandTester;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class CommandJobTest extends TestCase
{

	/** @var string[] */
	protected array $inputs;

	protected CommandTester $commandTester;

	public function setUp(): void
	{
		$this->inputs = [
			'--root-directory' => OUTPUT_DIR,
			'--root-namespace' => 'My\\App',
			'name' => 'Test',
			'--namespace' => 'My\\App\\My\\Commands',
			'--parent' => 'My\\App\\My\\Commands\\BaseCommand',
		];

		$this->commandTester = new CommandTester(
			new WorkerCommand(
				new CommandJob(
					new Config(['appDir' => ''])
				)
			)
		);
	}

	public function testNoninteractive(): void
	{
		Assert::same(0, $this->commandTester->run($this->inputs, false));

		Assert::same(file_get_contents(__DIR__ . '/expected/CommandJob.expect'), file_get_contents(OUTPUT_DIR . '/My/Commands/TestCommand.php'));
	}

	public function testInteractive(): void
	{
		Assert::same(0, $this->commandTester->run($this->inputs, true));

		Assert::same(file_get_contents(__DIR__ . '/expected/CommandJob.expect'), file_get_contents(OUTPUT_DIR . '/My/Commands/TestCommand.php'));
	}

	public function testInteractiveWithErrors(): void
	{
		$inputsWithErrors = [];

		foreach ($this->inputs as $input) {
			$inputsWithErrors[] = $input . '!';
			$inputsWithErrors[] = $input;
		}

		Assert::same(0, $this->commandTester->run($inputsWithErrors, true));

		Assert::same(file_get_contents(__DIR__ . '/expected/CommandJob.expect'), file_get_contents(OUTPUT_DIR . '/My/Commands/TestCommand.php'));
	}

}

(new CommandJobTest())->run();
