<?php
namespace Application\BLL;
/**
 * Class Factory
 * @package Application\BLL
 */
class Factory extends \Application\Component\Base
{
	/**
	 * @var Base[]
	 */
	private $components = array();

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
		$componentConfiguration =   $this->application->configuration->getBllComponentConfiguration($componentName);
		$componentClassName = $componentConfiguration['className'];
		$component = new $componentClassName($this->application);
		foreach($componentConfiguration as $key => $value)
		{
			$setter = 'set' . ucfirst($key);
			$component->$setter($value);
		}

		return $component;
	}
}