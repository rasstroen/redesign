<?php

namespace Application;

/**
 * Class Web
 * @package Application
 *
 * @property \Application\Component\Configuration\Base  $configuration
 * @property \Application\Component\Request\Web         $request
 * @property \Application\Component\Routing\Web         $routing
 * @property \Application\Component\Routing\UrlManager  $urlManager
 * @property \Application\Component\Image\Converter     $imageConverter
 * @property \Application\Component\Controller\Web      $controller
 * @property \Application\BLL\BLL                       $bll
 * @property \Application\Component\Database\Base       $db
 */
class Web
{
	/**
	 * @var \Application\Component\Base[]
	 */
	private $components = array();

	public function __construct(array $configuration)
	{
		$this->components['configuration'] = new \Application\Component\Configuration\Base($this);
		$this->configuration->setConfiguration($configuration);
	}

	public function run()
	{
		/**
		 * Выполняем запрос
		 */
		$this->controller->processRequest();
		/**
		 * Отрисовываем ответ
		 */
		$this->controller->render();
	}

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