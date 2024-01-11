<?php declare(strict_types = 1);

use Nette\Utils\Random;
use Ninjify\Nunjuck\Environment;
use Tester\Helpers;

require __DIR__ . '/../vendor/autoload.php';

// Configure Nette\Tester
Environment::setupTester();

// Configure timezone (Europe/Prague by default)
Environment::setupTimezone();

// Configure many constants
Environment::setupVariables(__DIR__);

// Fill global variables
Environment::setupGlobalVariables();

define('OUTPUT_DIR', sys_get_temp_dir() . '/' . Random::generate());
Helpers::purge(OUTPUT_DIR);
