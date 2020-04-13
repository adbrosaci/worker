<?php declare(strict_types = 1);

namespace Adbros\Worker\Tests\Jobs;

use Adbros\Worker\Command\WorkerCommand;
use Adbros\Worker\FileManager;
use Adbros\Worker\Jobs\PresenterJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class PresenterJobTest extends JobsTestCase
{

	public function setUp(): void
	{
		$this->inputs = [
			'--root-directory' => OUTPUT_DIR,
			'--root-namespace' => 'My\\App',
			'name' => 'Test',
			'--namespace' => 'My\\App\\My\\Presenters',
			'--parent' => 'My\\App\\My\\Presenters\\BasePresenter',
		];
	}

	public function testNoninteractive(): void
	{
		$fileManager = new FileManager('');

		$command = new WorkerCommand(new PresenterJob($fileManager));

		$input = new ArrayInput($this->inputs);

		$output = new BufferedOutput();

		Assert::same(0, $command->run($input, $output));

		Assert::same(file_get_contents(__DIR__ . '/expected/PresenterJob.expect'), file_get_contents(OUTPUT_DIR . '/My/Presenters/TestPresenter.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/PresenterJob.template.expect'), file_get_contents(OUTPUT_DIR . '/My/Presenters/templates/Test/default.latte'));
	}

	public function testInteractive(): void
	{
		$fileManager = new FileManager('');

		$command = new WorkerCommand(new PresenterJob($fileManager));

		$input = new ArrayInput([]);

		$input->setStream($this->createStream($this->inputs));

		$output = new BufferedOutput();

		Assert::same(0, $command->run($input, $output));

		Assert::same(file_get_contents(__DIR__ . '/expected/PresenterJob.expect'), file_get_contents(OUTPUT_DIR . '/My/Presenters/TestPresenter.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/PresenterJob.template.expect'), file_get_contents(OUTPUT_DIR . '/My/Presenters/templates/Test/default.latte'));
	}

}

(new PresenterJobTest())->run();
