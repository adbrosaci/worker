<?php declare(strict_types = 1);

namespace Adbros\Worker\Tests\Jobs;

use Adbros\Worker\Console\Command\WorkerCommand;
use Adbros\Worker\Job\PresenterJob;
use Adbros\Worker\Tests\CommandTester;
use Adbros\Worker\Util\FileManager;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class PresenterJobTest extends TestCase
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
			'--namespace' => 'My\\App\\My\\Presenters',
			'--parent' => 'My\\App\\My\\Presenters\\BasePresenter',
		];

		$this->commandTester = new CommandTester(
			new WorkerCommand(
				new PresenterJob(
					new FileManager('')
				)
			)
		);
	}

	public function testNoninteractive(): void
	{
		Assert::same(0, $this->commandTester->run($this->inputs, false));

		Assert::same(file_get_contents(__DIR__ . '/expected/PresenterJob.expect'), file_get_contents(OUTPUT_DIR . '/My/Presenters/TestPresenter.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/PresenterJob.template.expect'), file_get_contents(OUTPUT_DIR . '/My/Presenters/templates/Test/default.latte'));
	}

	public function testInteractive(): void
	{
		Assert::same(0, $this->commandTester->run($this->inputs, true));

		Assert::same(file_get_contents(__DIR__ . '/expected/PresenterJob.expect'), file_get_contents(OUTPUT_DIR . '/My/Presenters/TestPresenter.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/PresenterJob.template.expect'), file_get_contents(OUTPUT_DIR . '/My/Presenters/templates/Test/default.latte'));
	}

	public function testInteractiveWithErrors(): void
	{
		$inputsWithErrors = [];

		foreach ($this->inputs as $input) {
			$inputsWithErrors[] = $input . '!';
			$inputsWithErrors[] = $input;
		}

		Assert::same(0, $this->commandTester->run($inputsWithErrors, true));

		Assert::same(file_get_contents(__DIR__ . '/expected/PresenterJob.expect'), file_get_contents(OUTPUT_DIR . '/My/Presenters/TestPresenter.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/PresenterJob.template.expect'), file_get_contents(OUTPUT_DIR . '/My/Presenters/templates/Test/default.latte'));
	}

}

(new PresenterJobTest())->run();
