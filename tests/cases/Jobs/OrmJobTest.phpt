<?php declare(strict_types = 1);

namespace Adbros\Worker\Tests\Jobs;

use Adbros\Worker\Config\Config;
use Adbros\Worker\Console\Command\WorkerCommand;
use Adbros\Worker\Job\OrmJob;
use Adbros\Worker\Tests\CommandTester;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class OrmJobTest extends TestCase
{

	/** @var string[] */
	protected array $inputs;

	protected CommandTester $commandTester;

	public function setUp(): void
	{
		$this->inputs = [
			'--root-directory' => OUTPUT_DIR,
			'--root-namespace' => 'My\\App',
			'entity' => 'Test',
			'repository' => 'Tests',
			'--namespace' => 'My\\App\\My\\Model\\Orm\\Test',
			'--entity-parent' => 'My\\App\\My\\Model\\Orm\\BaseEntity',
			'--repository-parent' => 'My\\App\\My\\Model\\Orm\\BaseRepository',
			'--mapper-parent' => 'My\\App\\My\\Model\\Orm\\BaseMapper',
		];

		$this->commandTester = new CommandTester(
			new WorkerCommand(
				new OrmJob(
					new Config(['appDir' => ''])
				)
			)
		);
	}

	public function testNoninteractive(): void
	{
		Assert::same(0, $this->commandTester->run($this->inputs, false));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.entity.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/Test.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.mapper.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/TestsMapper.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.repository.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/TestsRepository.php'));
	}

	public function testInteractive(): void
	{
		Assert::same(0, $this->commandTester->run($this->inputs, true));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.entity.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/Test.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.mapper.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/TestsMapper.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.repository.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/TestsRepository.php'));
	}

	public function testInteractiveWithErrors(): void
	{
		$inputsWithErrors = [];

		foreach ($this->inputs as $input) {
			$inputsWithErrors[] = $input . '!';
			$inputsWithErrors[] = $input;
		}

		Assert::same(0, $this->commandTester->run($inputsWithErrors, true));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.entity.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/Test.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.mapper.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/TestsMapper.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.repository.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/TestsRepository.php'));
	}

}

(new OrmJobTest())->run();
