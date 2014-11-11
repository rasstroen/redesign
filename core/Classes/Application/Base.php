<?php

namespace Application;
/**
 * Class Base
 * @package Application
 *
 * @property \Application\Component\Configuration\Base  $configuration
 * @property \Application\Component\Request\Http        $httpRequest
 * @property \Application\Component\Routing\UrlManager  $urlManager
 * @property \Application\Component\Image\Converter     $imageConverter
 * @property \Application\BLL\BLL                       $bll
 */
abstract class Base
{
	/**
	 * @var \Application\Component\Base[]
	 */
	protected $components = array();

	public function __construct(array $configuration)
	{
		$this->components['configuration'] = new \Application\Component\Configuration\Base($this);
		$this->configuration->setConfiguration($configuration);
	}

	abstract function run();

	public function __get($componentName)
	{
		if(!isset($this->components[$componentName]))
		{
			$this->components[$componentName] = $this->createComponent($componentName);
		}

		return $this->components[$componentName];
	}

	private function createComponent($componentName)
	{
		$componentConfiguration =   $this->configuration->getComponentConfiguration($componentName);
		$componentClassName = $componentConfiguration['className'];
		$component = new $componentClassName($this);
		foreach($componentConfiguration as $key => $value)
		{
			$setter = 'set' . ucfirst($key);
			$component->$setter($value);
		}

		return $component;
	}
}