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
		$handle = fopen('/tmp/workers_log', 'a');
		$s = getmypid() . ' ' . date('Y-m-d H:i:s') .
			"\t" .
			memory_get_usage(true) / 1024 . 'Kb' .
			"\t" .
			memory_get_peak_usage(true) / 1024 . 'Kb' .
			"\t" .
			$message . "\n";
		echo $s;
		fwrite($handle, $s);
		fclose($handle);
	}
}