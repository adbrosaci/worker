<?php declare(strict_types = 1);

namespace Adbros\Worker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Inspired by Symfony\Bundle\MakerBundle\MakerInterface
 */
interface IJob
{

	/**
	 * Return the command name for your maker (e.g. make:report).
	 */
	public static function getCommandName(): string;

	/**
	 * Configure the command: set description, input arguments, options, etc.
	 *
	 * By default, all arguments will be asked interactively. If you want
	 * to avoid that, use the $inputConfig->setArgumentAsNonInteractive() method.
	 */
	public function configureCommand(Command $command): void;

	/**
	 * If necessary, you can use this method to interactively ask the user for input.
	 */
	public function interact(InputInterface $input, SymfonyStyle $io, Command $command): void;

	/**
	 * Called after normal code generation: allows you to do anything.
	 */
	public function generate(InputInterface $input, SymfonyStyle $io): int;

}
