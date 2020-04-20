<?php declare(strict_types = 1);

namespace Adbros\Worker\DI;

use Adbros\Worker\Config\Config;
use Adbros\Worker\Console\Command\WorkerCommand;
use Adbros\Worker\Job\IJob;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\ConsoleEvents;

class WorkerExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$container = $this->getContainerBuilder();

		$container->addDefinition($this->prefix('fileManager'))
			->setFactory(Config::class, [
				[
					'appDir' => $container->parameters['appDir'],
				],
			]);

		if (method_exists($this->compiler, 'loadDefinitionsFromConfig')) {
			$this->compiler->loadDefinitionsFromConfig(
				$this->loadFromFile(__DIR__ . '/WorkerExtension.neon')['services']
			);
		} else {
			$this->compiler->loadDefinitions(
				$container,
				$this->loadFromFile(__DIR__ . '/WorkerExtension.neon')['services'],
				$this->name
			);
		}

		foreach ($container->findByType(IJob::class) as $key => $job) {
			$container->addDefinition($this->prefix('jobs.' . $key))
				->setFactory(WorkerCommand::class, [$job])
				->addTag(ConsoleEvents::COMMAND, ['name' => $job->getClass()::getCommandName()]);
		}
	}

}
