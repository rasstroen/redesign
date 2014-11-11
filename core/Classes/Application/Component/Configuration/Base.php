<?php

namespace Application\Component\Configuration;

class Base extends \Application\Component\Base
{
	private $configuration;

	/**
	 * @param array $configuration
	 */
	public function setConfiguration(array $configuration)
	{
		$this->configuration = $configuration;
	}

	public function getDbConfiguration($baseName)
	{
		return $this->configuration['db'][$baseName];
	}

	public function getRootPath()
	{
		return $this->configuration['rootPath'];
	}

	/**
	 * @param $pageKey
	 * @return array
	 * @throws \Exception
	 */
	public function getPageConfiguration($pageKey)
	{
		if(!isset($this->configuration['routing']['pages'][$pageKey]))
		{
			throw new \Exception('Cant find configuration for page: ' . $pageKey);
		}
		return $this->configuration['routing']['pages'][$pageKey];
	}

	/**
	 * @return array
	 */
	public function getRoutingMap()
	{
		return $this->configuration['routing']['map'];
	}

	public function getBllComponentConfiguration($componentName)
	{
		if(!isset($this->configuration['bll'][$componentName]))
		{
			throw new \Exception('Cant find configuration for bll component: ' . $componentName);
		}
		return $this->configuration['bll'][$componentName];
	}

	/**
	 * @param $componentName
	 * @return array
	 * @throws \Exception
	 */
	public function getComponentConfiguration($componentName)
	{
		if(!isset($this->configuration['components'][$componentName]))
		{
			throw new \Exception('Cant find configuration for component: ' . $componentName);
		}
		return $this->configuration['components'][$componentName];
	}
}