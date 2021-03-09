<?php

// Load vendor classes
require __DIR__ . '/../vendor/autoload.php';

// Load all needed classes
require 'api.php';

// Load all needed functions
require 'api_funcs.php';

// Get app configurations
$config = include(__DIR__ . '/../config.php');

// Set variables
$app = $config->app;
$debug = '';
$info = '';
$error = '';

// Set timezone
date_default_timezone_set($app['timezone']);

// Set CONSTANTS
define("DEBUG", $app['debug']);
