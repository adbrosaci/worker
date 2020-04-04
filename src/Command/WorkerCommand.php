<?php declare(strict_types = 1);

namespace MartenB\Worker\Command;

use MartenB\Worker\ConsoleStyle;
use MartenB\Worker\IJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WorkerCommand extends Command
{

	/** @var IJob */
	private $job;

	/** @var ConsoleStyle */
	private $io;

	public function __construct(IJob $job)
	{
		$this->job = $job;

		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->setName($this->job::getCommandName());

		$this->job->configureCommand($this);
	}

	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$this->io = new ConsoleStyle($input, $output);
	}

	protected function interact(InputInterface $input, OutputInterface $output): void
	{
		$this->job->interact($input, $this->io, $this);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->job->generate($input, $this->io);

		return 0;
	}

}
