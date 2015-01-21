<?php

namespace Application;

/**
 * Class Console
 * @package Application
 *
 * @property \Application\Component\Configuration\Base  $configuration
 * @property \Application\Component\Routing\UrlManager  $urlManager
 * @property \Application\Component\Image\Converter     $imageConverter
 * @property \Application\Component\Youtube             $youtube
 */

class Console extends Base
{
	private $arguments = array();

	public function __construct(array $configuration)
	{
		parent::__construct($configuration);
		$this->prepareArguments();
	}

	public function run()
	{
		$classNameArray     = array();
		$classNameParts     = explode('-', $this->getConsoleArgument(1));
		foreach($classNameParts as $classNamePart)
		{
			$classNameArray[] = ucfirst($classNamePart);
		}
		$commandName        = '\Application\Command\\' . implode('\\', $classNameArray);
		$command            = new $commandName($this, $this->arguments);
		$methodName         = 'action';
		$methodNameParts    = explode('-', $this->getConsoleArgument(2));
		foreach($methodNameParts as $methodNamePart)
		{
			$methodName .= ucfirst($methodNamePart);
		}

		$command->$methodName();
	}

	public function getConsoleArgument($key)
	{
		return $this->arguments[$key];
	}

	private function prepareArguments()
	{
		global $argv;
		foreach($argv as $argument)
		{
			$argument = explode('=', str_replace('--', '', $argument));
			if(count($argument) > 1)
			{
				$this->arguments[$argument[0]] = $argument[1];
			}
			else
			{
				$this->arguments[] = $argument[0];
			}
		}
	}
}