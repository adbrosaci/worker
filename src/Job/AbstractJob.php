<?php declare(strict_types = 1);

namespace Adbros\Worker\Job;

use Adbros\Worker\Config\Config;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractJob implements IJob
{

	public const PHP_IDENT = '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';

	/** @var Config */
	private $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function configureCommand(Command $command): void
	{
		$command
			->addOption('root-directory', 'rdir', InputOption::VALUE_OPTIONAL, 'Namespace root directory')
			->addOption('root-namespace', 'rns', InputOption::VALUE_OPTIONAL, 'PSR-4 namespace root');
	}

	public function interact(InputInterface $input, SymfonyStyle $io, Command $command): void
	{
		if (!is_string($input->getOption('root-directory')) || !is_dir($input->getOption('root-directory'))) {
			$directory = $io->ask('Enter namespace root directory', $this->config->options['rootDirectory'] ?? 'app', function (?string $answer): string {
				if (!is_string($answer) || !is_dir($answer)) {
					throw new InvalidOptionException(sprintf('Please, enter valid namespace root directory. Directory "%s" does not exists.', $answer));
				}

				return $answer;
			});

			$input->setOption('root-directory', $directory);
		}

		if (!$this->isNamespace($input->getOption('root-namespace'))) {
			$namespace = $io->ask('Enter PSR-4 namespace root', $this->config->options['rootNamespace'] ?? 'App', function (?string $answer): string {
				if (!$this->isNamespace($answer)) {
					throw new InvalidOptionException(sprintf('Please, enter valid PSR-4 namespace root. PSR-4 namespace root "%s" is not valid.', $answer));
				}

				return $answer;
			});

			$input->setOption('root-namespace', $namespace);
		}
	}

	/**
	 * @return mixed[]
	 */
	protected function getConfig(): array
	{
		return $this->config->options[$this::getCommandName()] ?? [];
	}

	/**
	 * @param mixed $value
	 */
	protected function isClass($value): bool
	{
		return is_string($value) && preg_match('#^' . self::PHP_IDENT . '$#D', $value) === 1;
	}

	/**
	 * @param mixed $value
	 * @param mixed $rootNamespace
	 */
	protected function isNamespace($value, $rootNamespace = null): bool
	{
		if ($rootNamespace !== null && !$this->isNamespace($rootNamespace)) {
			return false;
		}

		return is_string($value) && preg_match('#^' . self::PHP_IDENT . '(\\\\' . self::PHP_IDENT . ')*$#D', $value) === 1;
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
