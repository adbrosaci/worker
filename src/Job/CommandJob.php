<?php declare(strict_types = 1);

namespace Adbros\Worker\Job;

use Nette\PhpGenerator\PhpFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommandJob extends AbstractJob
{

	public static function getCommandName(): string
	{
		return 'command';
	}

	public function configureCommand(Command $command): void
	{
		parent::configureCommand($command);

		$command
			->setDescription('Generate command for symfony/console package.')
			->addArgument('name', InputArgument::OPTIONAL, 'Command name')
			->addOption('namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Command namespace')
			->addOption('parent', 'p', InputOption::VALUE_OPTIONAL, 'Command parent class');
	}

	public function interact(InputInterface $input, SymfonyStyle $io, Command $command): void
	{
		parent::interact($input, $io, $command);

		if (!$this->isClass($input->getArgument('name'))) {
			$name = $io->ask('Enter command name', null, function (?string $answer): string {
				if (!$this->isClass($answer)) {
					throw new InvalidArgumentException('Please, enter valid command name.');
				}

				return $answer;
			});

			$input->setArgument('name', $name);
		}

		if (!$this->isNamespace($input->getOption('namespace'), $input->getOption('root-namespace'))) {
			$namespace = $io->ask('Enter command namespace', $input->getOption('root-namespace') . '\\Commands', function (?string $answer) use ($input): string {
				if (!$this->isNamespace($answer, $input->getOption('root-namespace'))) {
					throw new InvalidOptionException('Please, enter valid command namespace.');
				}

				return $answer;
			});

			$input->setOption('namespace', $namespace);
		}

		if (!$this->isNamespace($input->getOption('parent'))) {
			$namespace = $io->ask('Enter command parent class', 'Symfony\\Component\\Console\\Command\\Command', function (?string $answer): string {
				if (!$this->isNamespace($answer)) {
					throw new InvalidOptionException('Please, enter valid command parent class.');
				}

				return $answer;
			});

			$input->setOption('parent', $namespace);
		}
	}

	public function generate(InputInterface $input, SymfonyStyle $io): int
	{
		$directory = $input->getOption('root-directory') .
			$this->namespaceToPath($input->getOption('namespace'), $input->getOption('root-namespace'));

		if (!file_exists($directory)) {
			mkdir($directory, 0777, true);
		}

		$filename = $directory . '/' . $input->getArgument('name') . 'Command.php';

		if (file_exists($filename)) {
			throw new RuntimeException(sprintf('File %s already exists!', $filename));
		}

		$file = new PhpFile();

		if (method_exists($file, 'setStrictTypes')) {
			$file->setStrictTypes(true);
		}

		$namespace = $file
			->addNamespace($input->getOption('namespace'))
			->addUse($input->getOption('parent'));

		$class = $namespace
			->addClass($input->getArgument('name') . 'Command')
			->setExtends($input->getOption('parent'));

		$method = $class
			->addMethod('execute')
			->setVisibility('protected')
			->setReturnType('int')
			->setBody('return 0;');

		$parameter = $method->addParameter('input');

		if (method_exists($parameter, 'setType')) {
			$namespace->addUse('Symfony\\Component\\Console\\Input\\InputInterface');
			$parameter->setType('Symfony\\Component\\Console\\Input\\InputInterface');
		}

		$parameter = $method->addParameter('output');

		if (method_exists($parameter, 'setType')) {
			$namespace->addUse('Symfony\\Component\\Console\\Output\\OutputInterface');
			$parameter->setType('Symfony\\Component\\Console\\Output\\OutputInterface');
		}

		file_put_contents($filename, (string) $file);

		$io->text(sprintf('File %s created.', $filename));

		$io->success('Done.');

		return 0;
	}

}
