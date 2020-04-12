<?php declare(strict_types = 1);

namespace Adbros\Worker\Jobs;

use Adbros\Worker\FileManager;
use Adbros\Worker\IJob;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
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
		if ($input->getOption('root-directory') === null || !is_dir($input->getOption('root-directory'))) {
			$directory = $io->ask('Enter namespace root directory', $this->fileManager->appDir, function (?string $answer): string {
				if ($answer === null || !is_dir($answer)) {
					throw new InvalidOptionException(sprintf('Please, enter valid namespace root directory. Directory "%s" does not exists.', $answer));
				}

				return $answer;
			});

			$input->setOption('root-directory', $directory);
		}

		if ($input->getOption('root-namespace') === null || !$this->isNamespace($input->getOption('root-namespace'))) {
			$namespace = $io->ask('Enter PSR-4 namespace root', 'App', function (?string $answer): string {
				if ($answer === null || !$this->isNamespace($answer)) {
					throw new InvalidOptionException(sprintf('Please, enter valid PSR-4 namespace root. PSR-4 namespace root "%s" is not valid.', $answer));
				}

				return $answer;
			});

			$input->setOption('root-namespace', $namespace);
		}
	}

	protected function isClass(string $class): bool
	{
		return is_array(Strings::match($class, '~^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$~'));
	}

	protected function isNamespace(string $namespace, ?string $rootNamespace = null): bool
	{
		$items = explode('\\', $namespace);

		foreach ($items as $item) {
			if (!$this->isClass($item)) {
				return false;
			}
		}

		return $rootNamespace === null || Strings::startsWith($namespace, $rootNamespace);
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
