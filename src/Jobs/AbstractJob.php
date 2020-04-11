<?php declare(strict_types = 1);

namespace Adbros\Worker\Jobs;

use Adbros\Worker\FileManager;
use Adbros\Worker\IJob;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractJob implements IJob
{

	/** @var FileManager */
	private $fileManager;

	public function __construct(FileManager $fileManager)
	{
		$this->fileManager = $fileManager;
	}

	public function configureCommand(Command $command): void
	{
		$command
			->addOption('root-directory', 'rdir', InputOption::VALUE_OPTIONAL, 'Namespace root directory')
			->addOption('root-namespace', 'rns', InputOption::VALUE_OPTIONAL, 'PSR-4 namespace root');
	}

	public function interact(InputInterface $input, SymfonyStyle $io, Command $command): void
	{
		if ($input->getOption('root-directory') === null) {
			$directory = $io->ask('Enter namespace root directory', $this->fileManager->appDir);

			$input->setOption('root-directory', $directory);
		}

		if ($input->getOption('root-namespace') === null) {
			$namespace = $io->ask('Enter PSR-4 namespace root', 'App');

			$input->setOption('root-namespace', $namespace);
		}
	}

	protected function namespaceToPath(string $namespace, string $rootNamespace): string
	{
		return Strings::replace(
			Strings::replace(
				$namespace,
				'~^' . preg_quote($rootNamespace, '~') . '~'
			),
			'~\\\~',
			DIRECTORY_SEPARATOR
		);
	}

}
