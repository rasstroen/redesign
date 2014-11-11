<?php
header('X-Accel-Expires: 0');
header('Content-type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', true);

$configuration  = require_once '../config/localconfig.php';
$rootPath = $configuration['rootPath'];

require_once $rootPath . 'core/Classes/Autoload.php';
$autoloader = new Autoload($rootPath, $configuration['autoload']['directories']);

$application = new \Application\Web($configuration);
$application->run();
