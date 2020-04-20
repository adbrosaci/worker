<?php declare(strict_types = 1);

namespace Adbros\Worker\Console;

use Adbros\Worker\Console\Command\WorkerCommand;
use Adbros\Worker\Job\CommandJob;
use Adbros\Worker\Job\ControlJob;
use Adbros\Worker\Job\OrmJob;
use Adbros\Worker\Job\PresenterJob;
use Adbros\Worker\Util\FileManager;
use Symfony\Component\Console\Application;

class WorkerApplication extends Application
{

	public function __construct()
	{
		parent::__construct('Worker by Adbros');

		$fileManager = new FileManager('');

		$this->addCommands([
			new WorkerCommand(new CommandJob($fileManager)),
			new WorkerCommand(new ControlJob($fileManager)),
			new WorkerCommand(new OrmJob($fileManager)),
			new WorkerCommand(new PresenterJob($fileManager)),
		]);
	}

}
