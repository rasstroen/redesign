<?php
namespace Application\Component;

use Application\Web;

class Base
{
	/**
	 * @var Web
	 */
	protected $application;

	/**
	 * @var string
	 */
	private $className;

	public function __construct(\Application\Base $application)
	{
		$this->application = $application;
		$this->initialize();
	}

	protected function initialize()
	{

	}

	public function setClassName($className)
	{
		$this->className = $className;
	}
}