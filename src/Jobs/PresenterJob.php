<?php declare(strict_types = 1);

namespace Adbros\Worker\Jobs;

use Nette\PhpGenerator\PhpFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

class PresenterJob extends AbstractJob
{

	public static function getCommandName(): string
	{
		return 'worker:presenter';
	}

	public function configureCommand(Command $command): void
	{
		parent::configureCommand($command);

		$command
			->setDescription('Generate presenter and default template.')
			->addArgument('name', InputArgument::OPTIONAL, 'Presenter name')
			->addOption('namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Presenter namespace');
	}

	public function interact(InputInterface $input, SymfonyStyle $io, Command $command): void
	{
		parent::interact($input, $io, $command);

		if ($input->getArgument('name') === null) {
			$name = $io->ask('Enter presenter name', null, function (?string $answer): string {
				if ($answer === null || !$this->isClass($answer)) {
					throw new InvalidArgumentException('Please, enter valid presenter name.');
				}

				return $answer;
			});

			$input->setArgument('name', $name);
		}

		if ($input->getOption('namespace') === null) {
			$namespace = $io->ask('Enter presenter namespace', $input->getOption('root-namespace') . '\\Presenters', function (?string $answer) use ($input): string {
				if ($answer === null || !$this->isNamespace($answer, $input->getOption('root-namespace'))) {
					throw new InvalidOptionException('Please, enter valid presenter namespace.');
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

		$filename = $directory . '/' . $input->getArgument('name') . 'Presenter.php';

		if (file_exists($filename)) {
			throw new RuntimeException(sprintf('File %s already exists!', $filename));
		}

		$latteDirectory = $directory . '/templates/' . $input->getArgument('name');

		if (!file_exists($latteDirectory)) {
			mkdir($latteDirectory, 0777, true);
		}

		$latteFilename = $latteDirectory . '/default.latte';

		if (file_exists($latteFilename)) {
			throw new RuntimeException(sprintf('File %s already exists!', $latteFilename));
		}

		$file = new PhpFile();

		if (method_exists($file, 'setStrictTypes')) {
			$file->setStrictTypes(true);
		}

		$file
			->addNamespace($input->getOption('namespace'))
			->addUse('Nette\\Application\\UI\\Presenter')
			->addClass($input->getArgument('name') . 'Presenter')
			->setExtends('Nette\\Application\\UI\\Presenter');

		file_put_contents($filename, (string) $file);

		$io->text(sprintf('File %s created.', $filename));

		file_put_contents($latteFilename, '{block content}' . PHP_EOL);

		$io->text(sprintf('File %s created.', $latteFilename));

		$io->success('Done.');

		return 0;
	}

}
