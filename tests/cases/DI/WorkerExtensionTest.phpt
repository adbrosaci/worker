<?php declare(strict_types = 1);

namespace Adbros\Worker\Tests\DI;

use Adbros\Worker\Console\Command\WorkerCommand;
use Adbros\Worker\DI\WorkerExtension;
use Adbros\Worker\Job\IJob;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class WorkerExtensionTest extends TestCase
{

	public function testDefaults(): void
	{
		$loader = new ContainerLoader(TMP_DIR, true);
		$class = $loader->load(function (Compiler $compiler): void {
			$compiler->addExtension('worker', new WorkerExtension())
				->addConfig([
					'parameters' => [
						'appDir' => '',
					],
				]);
		}, 1);

		/** @var Container $container */
		$container = new $class();

		Assert::same(count($container->findByType(IJob::class)), count($container->findByType(WorkerCommand::class)));
	}

}

(new WorkerExtensionTest())->run();
