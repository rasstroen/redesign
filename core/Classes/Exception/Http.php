<?php
namespace Classes\Exception;

class Http extends \Exception
{
	protected $errorCode = 500;

	public function getHttpCode()
	{
		return $this->errorCode;
	}
}