<?php declare(strict_types = 1);

namespace Adbros\Worker\Tests\Jobs;

use Adbros\Worker\Command\WorkerCommand;
use Adbros\Worker\FileManager;
use Adbros\Worker\Jobs\ControlJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class ControlJobTest extends JobsTestCase
{

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
	}

	public function testNoninteractive(): void
	{
		$fileManager = new FileManager('');

		$command = new WorkerCommand(new ControlJob($fileManager));

		$input = new ArrayInput($this->inputs);

		$output = new BufferedOutput();

		Assert::same(0, $command->run($input, $output));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.control.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestControl.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.factory.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestFactory.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.template.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestControl.latte'));
	}

	public function testInteractive(): void
	{
		$fileManager = new FileManager('');

		$command = new WorkerCommand(new ControlJob($fileManager));

		$input = new ArrayInput([]);

		$input->setStream($this->createStream($this->inputs));

		$output = new BufferedOutput();

		Assert::same(0, $command->run($input, $output));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.control.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestControl.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.factory.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestFactory.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/ControlJob.template.expect'), file_get_contents(OUTPUT_DIR . '/My/Controls/Test/TestControl.latte'));
	}

}

(new ControlJobTest())->run();
