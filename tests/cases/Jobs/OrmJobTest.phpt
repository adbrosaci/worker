<?php declare(strict_types = 1);

namespace Adbros\Worker\Tests\Jobs;

use Adbros\Worker\Command\WorkerCommand;
use Adbros\Worker\FileManager;
use Adbros\Worker\Jobs\OrmJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class OrmJobTest extends JobsTestCase
{

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
	}

	public function testNoninteractive(): void
	{
		$fileManager = new FileManager('');

		$command = new WorkerCommand(new OrmJob($fileManager));

		$input = new ArrayInput($this->inputs);

		$output = new BufferedOutput();

		Assert::same(0, $command->run($input, $output));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.entity.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/Test.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.mapper.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/TestsMapper.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.repository.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/TestsRepository.php'));
	}

	public function testInteractive(): void
	{
		$fileManager = new FileManager('');

		$command = new WorkerCommand(new OrmJob($fileManager));

		$input = new ArrayInput([]);

		$input->setStream($this->createStream($this->inputs));

		$output = new BufferedOutput();

		Assert::same(0, $command->run($input, $output));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.entity.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/Test.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.mapper.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/TestsMapper.php'));

		Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.repository.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test/TestsRepository.php'));
	}

}

(new OrmJobTest())->run();
