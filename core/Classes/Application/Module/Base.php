<?php

namespace Application\Module;
use Application\Web;

class Base
{
	/**
	 * @var Web
	 */
	protected $application;

	function __construct(Web $application)
	{
		$this->application  = $application;
	}
}