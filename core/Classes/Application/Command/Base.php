<?php
namespace Application\Command;

use Application\Console;

class Base
{
	protected $arguments = array();
	/**
	 * @var Console
	 */
	protected $application;

	function __construct(Console $application, array $arguments)
	{
		$this->arguments    = $arguments;
		$this->application  = $application;
	}

	protected function log($message)
	{
		echo date('Y-m-d H:i:s');
		echo "\t";
		echo memory_get_usage(true) / 1024 . 'Kb';
		echo "\t";
		echo memory_get_peak_usage(true) / 1024 . 'Kb';
		echo "\t";
		echo $message . "\n";
	}
}