<?php
namespace Application\BLL;

use Application\Base;

/**
 * Class BLL
 * @package Application\BLL
 */
abstract class BLL
{
	private $className;
	/**
	 * @var Base
	 */
	protected $application;

	public function setClassName($className)
	{
		$this->className = $className;
	}

	function __construct(Base $application)
	{
		$this->application = $application;
	}

	/**
	 * @return \Application\Component\Database\PDODatabase
	 */
	protected function getDbWeb()
	{
		return $this->application->db->web;
	}

	protected function getDbMaster()
	{
		return $this->application->db->master;
	}
}