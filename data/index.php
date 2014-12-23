<?php
header('X-Accel-Expires: 0');
header('Content-type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', true);

if(isset($_GET['test']))
{
	require 'test.php';
	exit(0);
}

$configuration  = require_once '../config/localconfig.php';
$rootPath = $configuration['rootPath'];

require_once $rootPath . 'core/Classes/Autoload.php';
$autoloader = new Autoload($rootPath, $configuration['autoload']['directories']);

try
{
	$application = new \Application\Web($configuration);
	$application->run();
}
catch (\Classes\Exception\Http $e)
{
	if(!$application->getIsDevelopmentMode())
	{
		header('Error', null, $e->getHttpCode());
	}
	throw $e;
}
catch(Exception $e)
{
	if(!$application->getIsDevelopmentMode())
	{
		header('Error', null, 500);
	}
	throw $e;
}

