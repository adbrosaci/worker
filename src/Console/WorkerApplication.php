<?php declare(strict_types = 1);

namespace Adbros\Worker\Console;

use Adbros\Worker\Config\Config;
use Adbros\Worker\Config\ConfigLoader;
use Adbros\Worker\Console\Command\WorkerCommand;
use Adbros\Worker\Job\CommandJob;
use Adbros\Worker\Job\ControlJob;
use Adbros\Worker\Job\OrmJob;
use Adbros\Worker\Job\PresenterJob;
use Symfony\Component\Console\Application;

class WorkerApplication extends Application
{

	public function __construct()
	{
		parent::__construct('Worker by Adbros');

		$config = new Config(ConfigLoader::getConfiguration(getcwd()));

		$this->addCommands([
			new WorkerCommand(new CommandJob($config)),
			new WorkerCommand(new ControlJob($config)),
			new WorkerCommand(new OrmJob($config)),
			new WorkerCommand(new PresenterJob($config)),
		]);
	}

}
