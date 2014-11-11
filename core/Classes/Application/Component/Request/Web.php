<?php
namespace Application\Component\Request;

use Application\Component\Base;

class Web extends Base
{
	private $url;

	private $queryParams    = array();
	private $postParams     = array();

	protected function initialize()
	{
		parent::initialize();
		$this->url  = $_SERVER['REQUEST_URI'];
		$this->queryParams = $_GET;
		unset($_GET);
		$this->postParams = $_POST;
		unset($_POST);
	}

	public function redirect($url,$httpCode = 301)
	{
		header('Location: '. $url, true, $httpCode);
		return;
	}

	public function getQueryParam($paramName, $defaultValue = null)
	{
		return isset($this->queryParams[$paramName]) ? $this->queryParams[$paramName] : $defaultValue;
	}

	public function getQueryParams()
	{
		return $this->queryParams;
	}

	public function getPostParam($paramName, $defaultValue = null)
	{
		return isset($this->postParams[$paramName]) ? $this->postParams[$paramName] : $defaultValue;
	}

	public function getUrl()
	{
		return $this->url;
	}
}