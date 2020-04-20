<?php declare(strict_types = 1);

namespace Adbros\Worker\Tests\Jobs;

use Adbros\Worker\Config\Config;
use Adbros\Worker\Console\Command\WorkerCommand;
use Adbros\Worker\Job\ControlJob;
use Adbros\Worker\Tests\CommandTester;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class ControlJobTest extends TestCase
{

	/** @var string[] */
	protected $inputs;

	/** @var CommandTester */
	protected $commandTester;

	public function setUp(): void
	{
		$this->inputs = [
			'--root-directory' => OUTPUT_DIR,
			'--root-namespace' => 'My\\App',
			'name' => 'Test',
			'--namespace' => 'My\\App\\My\\Controls\\Test',
			'--control-parent' => 'My\\App\\My\\Controls\\BaseControl',
			'--factory-parent' => 'My\\App\\My\\Controls\\BaseFactory',
		];

		$this->commandTester = new CommandTester(
			new WorkerCommand(
				new ControlJob(
					new Config(['appDir' => ''])
				)
			)
		);
	}

	public function testNoninteractive(): void
	{
		Assert::same(0, $this->commandTester->run($this->inputs, false));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.control.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestControl.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.factory.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestFactory.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.template.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestControl.latte'));
	}

	public function testInteractive(): void
	{
		Assert::same(0, $this->commandTester->run($this->inputs, true));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.control.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestControl.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.factory.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestFactory.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.template.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestControl.latte'));
	}

	public function testInteractiveWithErrors(): void
	{
		$inputsWithErrors = [];

		foreach ($this->inputs as $input) {
			$inputsWithErrors[] = $input . '!';
			$inputsWithErrors[] = $input;
		}

		Assert::same(0, $this->commandTester->run($inputsWithErrors, true));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.control.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestControl.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.factory.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestFactory.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.template.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestControl.latte'));
	}

}

(new ControlJobTest())->run();
