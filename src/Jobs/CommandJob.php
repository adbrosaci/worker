<?php declare(strict_types = 1);

namespace Adbros\Worker\Jobs;

use Nette\PhpGenerator\PhpFile;
use Nette\Utils\Strings;
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
		return 'worker:command';
	}

	public function configureCommand(Command $command): void
	{
		parent::configureCommand($command);

		$command
			->setDescription('Generate command for symfony/command package.')
			->addArgument('name', InputArgument::OPTIONAL, 'Command name')
			->addOption('namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Command namespace');
	}

	public function interact(InputInterface $input, SymfonyStyle $io, Command $command): void
	{
		parent::interact($input, $io, $command);

		if ($input->getArgument('name') === null) {
			$entity = $io->ask('Enter command name', null, function (?string $answer): string {
				if ($answer === null) {
					throw new InvalidArgumentException('Please, enter command name.');
				}

				return $answer;
			});

			$input->setArgument('name', $entity);
		}

		if ($input->getOption('namespace') === null) {
			$namespace = $io->ask('Enter command namespace', $input->getOption('root-namespace') . '\\Commands', function (?string $answer) use ($input): string {
				if ($answer === null || !Strings::startsWith($answer, $input->getOption('root-namespace'))) {
					throw new InvalidOptionException('Command namespace must be part of root namespace.');
				}

				return $answer;
			});

			$input->setOption('namespace', $namespace);
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
			->addUse('Symfony\\Component\\Console\\Command\\Command');

		$class = $namespace
			->addClass($input->getArgument('name') . 'Command')
			->setExtends('Symfony\\Component\\Console\\Command\\Command');

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

		$io->success('Done.');

		return 0;
	}

}
