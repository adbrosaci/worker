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

class OrmJob extends AbstractJob
{

	public static function getCommandName(): string
	{
		return 'orm';
	}

	public function configureCommand(Command $command): void
	{
		parent::configureCommand($command);

		$command
			->setDescription('Generate model for nextras/orm package.')
			->addArgument('entity', InputArgument::OPTIONAL, 'Entity name')
			->addArgument('repository', InputArgument::OPTIONAL, 'Repository and mapper name')
			->addOption('namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Model namespace')
			->addOption('entity-parent', 'ep', InputOption::VALUE_OPTIONAL, 'Entity parent class')
			->addOption('repository-parent', 'rp', InputOption::VALUE_OPTIONAL, 'Repository parent class')
			->addOption('mapper-parent', 'mp', InputOption::VALUE_OPTIONAL, 'Mapper parent class');
	}

	public function interact(InputInterface $input, SymfonyStyle $io, Command $command): void
	{
		parent::interact($input, $io, $command);

		if (!$this->isClass($input->getArgument('entity'))) {
			$entity = $io->ask('Enter entity name', null, function (?string $answer): string {
				if (!$this->isClass($answer)) {
					throw new InvalidArgumentException('Please, enter valid entity name.');
				}

				return $answer;
			});

			$input->setArgument('entity', $entity);
		}

		if (!$this->isClass($input->getArgument('repository'))) {
			$repository = $io->ask('Enter repository and mapper name', $input->getArgument('entity') . 's', function (?string $answer): string {
				if (!$this->isClass($answer)) {
					throw new InvalidArgumentException('Please, enter valid repository name.');
				}

				return $answer;
			});

			$input->setArgument('repository', $repository);
		}

		if (!$this->isNamespace($input->getOption('namespace'), $input->getOption('root-namespace'))) {
			$namespace = $io->ask('Enter model namespace', $input->getOption('root-namespace') . '\\Model\\Orm\\' . $input->getArgument('entity'), function (?string $answer) use ($input): string {
				if (!$this->isNamespace($answer, $input->getOption('root-namespace'))) {
					throw new InvalidOptionException('Please, enter valid model namespace.');
				}

				return $answer;
			});

			$input->setOption('namespace', $namespace);
		}

		if (!$this->isNamespace($input->getOption('entity-parent'))) {
			$namespace = $io->ask('Enter entity parent class', 'Nextras\\Orm\\Entity\\Entity', function (?string $answer): string {
				if (!$this->isNamespace($answer)) {
					throw new InvalidOptionException('Please, enter valid entity parent class.');
				}

				return $answer;
			});

			$input->setOption('entity-parent', $namespace);
		}

		if (!$this->isNamespace($input->getOption('repository-parent'))) {
			$namespace = $io->ask('Enter repository parent class', 'Nextras\\Orm\\Repository\\Repository', function (?string $answer): string {
				if (!$this->isNamespace($answer)) {
					throw new InvalidOptionException('Please, enter valid repository parent class.');
				}

				return $answer;
			});

			$input->setOption('repository-parent', $namespace);
		}

		if (!$this->isNamespace($input->getOption('mapper-parent'))) {
			$namespace = $io->ask('Enter mapper parent class', 'Nextras\\Orm\\Mapper\\Mapper', function (?string $answer): string {
				if (!$this->isNamespace($answer)) {
					throw new InvalidOptionException('Please, enter valid mapper parent class.');
				}

				return $answer;
			});

			$input->setOption('mapper-parent', $namespace);
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
			->addUse($input->getOption('entity-parent'))
			->addClass($input->getArgument('entity'))
			->setExtends($input->getOption('entity-parent'))
			->addComment('@property int $id {primary}');

		file_put_contents(($filename = $directory . '/' . $input->getArgument('entity') . '.php'), (string) $file);

		$io->text(sprintf('File %s created.', $filename));

		$file = new PhpFile();

		if (method_exists($file, 'setStrictTypes')) {
			$file->setStrictTypes(true);
		}

		$file
			->addNamespace($input->getOption('namespace'))
			->addUse($input->getOption('repository-parent'))
			->addClass($input->getArgument('repository') . 'Repository')
			->setExtends($input->getOption('repository-parent'))
			->addMethod('getEntityClassNames')
			->setStatic(true)
			->setReturnType('array')
			->addComment('@return string[]')
			->setBody('return [' . $input->getArgument('entity') . '::class];');

		file_put_contents(($filename = $directory . '/' . $input->getArgument('repository') . 'Repository.php'), (string) $file);

		$io->text(sprintf('File %s created.', $filename));

		$file = new PhpFile();

		if (method_exists($file, 'setStrictTypes')) {
			$file->setStrictTypes(true);
		}

		$file
			->addNamespace($input->getOption('namespace'))
			->addUse($input->getOption('mapper-parent'))
			->addClass($input->getArgument('repository') . 'Mapper')
			->setExtends($input->getOption('mapper-parent'));

		file_put_contents(($filename = $directory . '/' . $input->getArgument('repository') . 'Mapper.php'), (string) $file);

		$io->text(sprintf('File %s created.', $filename));

		$io->success('Done.');

		return 0;
	}

}
