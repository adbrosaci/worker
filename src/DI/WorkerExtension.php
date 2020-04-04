<?php declare(strict_types = 1);

namespace MartenB\Worker\DI;

use MartenB\Worker\Command\WorkerCommand;
use MartenB\Worker\FileManager;
use MartenB\Worker\IJob;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\ConsoleEvents;

class WorkerExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$container = $this->getContainerBuilder();

		$container->addDefinition($this->prefix('fileManager'))
			->setFactory(FileManager::class, [$container->parameters['appDir']]);

		$this->compiler->loadDefinitionsFromConfig(
			$this->loadFromFile(__DIR__ . '/WorkerExtension.neon')['services']
		);

		foreach ($container->findByType(IJob::class) as $job) {
			$container->addDefinition($this->prefix('jobs.' . $job->getName()))
				->setFactory(WorkerCommand::class, [$job])
				->addTag(ConsoleEvents::COMMAND, ['name' => $job->getType()::getCommandName()]);
		}
	}

}
