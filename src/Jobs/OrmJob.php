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

class OrmJob extends AbstractJob
{

	public static function getCommandName(): string
	{
		return 'worker:orm';
	}

	public function configureCommand(Command $command): void
	{
		parent::configureCommand($command);

		$command
			->setDescription('Generate model for nextras/orm package.')
			->addArgument('entity', InputArgument::OPTIONAL, 'Entity name')
			->addArgument('repository', InputArgument::OPTIONAL, 'Repository and mapper name')
			->addOption('namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Model namespace');
	}

	public function interact(InputInterface $input, SymfonyStyle $io, Command $command): void
	{
		parent::interact($input, $io, $command);

		if ($input->getArgument('entity') === null) {
			$entity = $io->ask('Enter entity name', null, function (?string $answer): string {
				if ($answer === null) {
					throw new InvalidArgumentException('Please, enter entity name.');
				}

				return $answer;
			});

			$input->setArgument('entity', $entity);
		}

		if ($input->getArgument('repository') === null) {
			$repository = $io->ask('Enter repository and mapper name', $input->getArgument('entity') . 's');

			$input->setArgument('repository', $repository);
		}

		if ($input->getOption('namespace') === null) {
			$namespace = $io->ask('Enter model namespace', $input->getOption('root-namespace') . '\\Model\\Orm\\' . $input->getArgument('entity'), function (?string $answer) use ($input): string {
				if ($answer === null || !Strings::startsWith($answer, $input->getOption('root-namespace'))) {
					throw new InvalidOptionException('Model namespace must be part of root namespace.');
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

		if (file_exists($directory)) {
			throw new RuntimeException(sprintf('Directory %s already exists!', $directory));
		}

		mkdir($directory, 0777, true);

		$file = new PhpFile();

		if (method_exists($file, 'setStrictTypes')) {
			$file->setStrictTypes(true);
		}

		$file
			->addNamespace($input->getOption('namespace'))
			->addClass($input->getArgument('entity'))
			->setExtends('Nextras\\Orm\\Entity\\Entity')
			->addComment('@property int $id {primary}');

		file_put_contents($directory . '/' . $input->getArgument('entity') . '.php', (string) $file);

		$file = new PhpFile();

		if (method_exists($file, 'setStrictTypes')) {
			$file->setStrictTypes(true);
		}

		$file
			->addNamespace($input->getOption('namespace'))
			->addClass($input->getArgument('repository') . 'Repository')
			->setExtends('Nextras\\Orm\\Repository\\Repository')
			->addMethod('getEntityClassNames')
			->setStatic(true)
			->setReturnType('array')
			->addComment('@return string[]')
			->setBody('return [' . $input->getArgument('entity') . '::class];');

		file_put_contents($directory . '/' . $input->getArgument('repository') . 'Repository.php', (string) $file);

		$file = new PhpFile();

		if (method_exists($file, 'setStrictTypes')) {
			$file->setStrictTypes(true);
		}

		$file
			->addNamespace($input->getOption('namespace'))
			->addClass($input->getArgument('repository') . 'Mapper')
			->setExtends('Nextras\\Orm\\Mapper\\Mapper');

		file_put_contents($directory . '/' . $input->getArgument('repository') . 'Mapper.php', (string) $file);

		$io->success('Done.');

		return 0;
	}

}
