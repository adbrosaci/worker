<?php declare(strict_types = 1);

use Nette\Loaders\RobotLoader;
use Nette\Utils\Random;
use Ninjify\Nunjuck\Environment;

require __DIR__ . '/../vendor/autoload.php';

// Configure Nette\Tester
Environment::setupTester();

// Configure timezone (Europe/Prague by default)
Environment::setupTimezone();

// Configure many constants
Environment::setupVariables(__DIR__);

// Fill global variables
Environment::setupGlobalVariables();

// Register robot loader
Environment::setupRobotLoader(function (RobotLoader $robotLoader): void {
	$robotLoader->setAutoRefresh(true);
});

define('OUTPUT_DIR', sys_get_temp_dir() . '/' . Random::generate());

if (!is_dir(OUTPUT_DIR)) {
	mkdir(OUTPUT_DIR);
}
