<?php
namespace Application\Command\Tasks\Worker;

use Application\Console;

class Base
{
	/**
	 * @var Console
	 */
	protected $application;

	protected $tasks = array();

	function __construct(Console $application, array $worker)
	{
		$this->application  = $application;
		$this->tasks        = unserialize($worker['tasks']);
	}

	public function runCommand($method)
	{
		$functionName   = 'method' . ucfirst($method);
		foreach($this->tasks as $task)
		{
			$this->$functionName(unserialize($task['data']));
		}
	}

	protected function log($message)
	{
		$handle = fopen('/tmp/workers_log', 'a');
		$s = date('Y-m-d H:i:s') .
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