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

class ControlJob extends AbstractJob
{

	public static function getCommandName(): string
	{
		return 'worker:control';
	}

	public function configureCommand(Command $command): void
	{
		parent::configureCommand($command);

		$command
			->setDescription('Generate control with factory and template.')
			->addArgument('name', InputArgument::OPTIONAL, 'Control name')
			->addOption('namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Control namespace')
			->addOption('control-parent', 'cp', InputOption::VALUE_OPTIONAL, 'Control parent class')
			->addOption('factory-parent', 'fp', InputOption::VALUE_OPTIONAL, 'Factory parent class');
	}

	public function interact(InputInterface $input, SymfonyStyle $io, Command $command): void
	{
		parent::interact($input, $io, $command);

		if (!$this->isClass($input->getArgument('name'))) {
			$name = $io->ask('Enter control name', null, function (?string $answer): string {
				if (!$this->isClass($answer)) {
					throw new InvalidArgumentException('Please, enter valid control name.');
				}

				return $answer;
			});

			$input->setArgument('name', $name);
		}

		if (!$this->isNamespace($input->getOption('namespace'), $input->getOption('root-namespace'))) {
			$namespace = $io->ask('Enter control namespace', $input->getOption('root-namespace') . '\\Controls\\' . $input->getArgument('name'), function (?string $answer) use ($input): string {
				if (!$this->isNamespace($answer, $input->getOption('root-namespace'))) {
					throw new InvalidOptionException('Please, enter valid control namespace.');
				}

				return $answer;
			});

			$input->setOption('namespace', $namespace);
		}

		if (!$this->isNamespace($input->getOption('control-parent'))) {
			$namespace = $io->ask('Enter control parent class', 'Nette\\Application\\UI\\Control', function (?string $answer): string {
				if (!$this->isNamespace($answer)) {
					throw new InvalidOptionException('Please, enter valid control parent class.');
				}

				return $answer;
			});

			$input->setOption('control-parent', $namespace);
		}

		if ($input->getOption('factory-parent') !== '' && !$this->isNamespace($input->getOption('factory-parent'))) {
			$namespace = $io->ask('Enter factory parent class', '', function (?string $answer): string {
				if ($answer !== '' && !$this->isNamespace($answer)) {
					throw new InvalidOptionException('Please, enter valid factory parent class.');
				}

				return $answer;
			});

			$input->setOption('factory-parent', $namespace);
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
			->addUse($input->getOption('control-parent'))
			->addClass($input->getArgument('name') . 'Control')
			->setExtends($input->getOption('control-parent'))
			->addMethod('render')
			->setReturnType('void')
			->setBody('$this->template->setFile(\'' . $input->getArgument('name') . 'Control.latte\');' . "\n" . '$this->template->render();');

		file_put_contents(($filename = $directory . '/' . $input->getArgument('name') . 'Control.php'), (string) $file);

		$io->text(sprintf('File %s created.', $filename));

		$file = new PhpFile();

		if (method_exists($file, 'setStrictTypes')) {
			$file->setStrictTypes(true);
		}

		$namespace = $file
			->addNamespace($input->getOption('namespace'));

		if ($input->getOption('factory-parent') !== '') {
			$namespace
				->addUse($input->getOption('factory-parent'));
		}

		$class = $namespace
			->addClass($input->getArgument('name') . 'Control');

		if ($input->getOption('factory-parent') !== '') {
			$class
				->setExtends($input->getOption('factory-parent'));
		}

		$class->addMethod('create')
			->setReturnType($input->getOption('namespace') . '\\' . $input->getArgument('name') . 'Control')
			->setBody('return new ' . $input->getArgument('name') . 'Control();');

		file_put_contents(($filename = $directory . '/' . $input->getArgument('name') . 'Factory.php'), (string) $file);

		$io->text(sprintf('File %s created.', $filename));

		file_put_contents(($filename = $directory . '/' . $input->getArgument('name') . 'Control.latte'), '');

		$io->text(sprintf('File %s created.', $filename));

		return 0;
	}

}
