<?php declare(strict_types = 1);

use Adbros\Worker\Command\WorkerCommand;
use Adbros\Worker\FileManager;
use Adbros\Worker\Jobs\OrmJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	$fileManager = new FileManager('');

	$command = new WorkerCommand(new OrmJob($fileManager));

	$input = new ArrayInput([
		'entity' => 'Test',
		'repository' => 'Tests',
		'--namespace' => 'My\\App\\My\\Model\\Orm',
		'--root-directory' => OUTPUT_DIR,
		'--root-namespace' => 'My\\App',
	]);

	$output = new BufferedOutput();

	Assert::same(0, $command->run($input, $output));

	Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.entity.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/Test.php'));

	Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.mapper.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/TestsMapper.php'));

	Assert::same(file_get_contents(__DIR__ . '/expected/OrmJob.repository.expect'), file_get_contents(OUTPUT_DIR . '/My/Model/Orm/TestsRepository.php'));
});