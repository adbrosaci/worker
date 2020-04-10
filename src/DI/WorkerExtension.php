<?php declare(strict_types = 1);

namespace Adbros\Worker\DI;

use Adbros\Worker\Command\WorkerCommand;
use Adbros\Worker\FileManager;
use Adbros\Worker\IJob;
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
