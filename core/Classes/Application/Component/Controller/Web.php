<?php

namespace Application\Component\Controller;

use Application\Component\Base;

class Web extends Base
{
	/**
	 * Результат выполнения модулей
	 * @var array
	 */
	private $data;

	public function processWrite($writeModule)
	{
		$method = $this->application->request->getPostParam('method');
		if(!$method)
		{
			$method = $this->application->request->getQueryParam('method');
		}
		$moduleName         = explode('/', $writeModule);
		$moduleClassName    = 'Application\Module\\' .ucfirst($moduleName[0]) . '\\' . ucfirst($moduleName[1]);
		$module             =  new $moduleClassName($this->application);
		$writeAction        = 'do' . ucfirst($method);
		$module->$writeAction();
	}

	public function processRequest()
	{
		/**
		 * Модули записи
		 */
		if(
		($writeModule = $this->application->request->getPostParam('writemodule'))||
			$writeModule = $this->application->request->getQueryParam('writemodule')
		)
		{
			$this->processWrite($writeModule);
		}

		list($pageKey, $urlVariables) = $this->application->routing->getPageKeyAndVariables($this->application->request->getUrl());
		$pageConfiguration  = $this->application->configuration->getPageConfiguration($pageKey);
		$data = array();
		/**
		 * Выполняем модули
		 */
		foreach($pageConfiguration['blocks'] as $blockName => $modules)
		{
			$data[$blockName] = array();
			foreach($modules as $key => $moduleConfiguration)
			{
				$data[$blockName][$key] = array(
					'configuration' => $moduleConfiguration
				);
				$moduleVariables = $urlVariables;
				if(!empty($moduleConfiguration['variables']))
				{
					$moduleVariables += $moduleConfiguration['variables'];
				}
				$data[$blockName][$key]['data'] = $this->processModule($moduleConfiguration, $moduleVariables);
			}
		}

		$this->application->view->setConfiguration($pageConfiguration);
		$this->application->view->setLayout($pageConfiguration['layout']);
		$this->data = $data;
	}

	public function render()
	{
		$this->application->view->render($this->data);
	}

	private function processModule(array $moduleConfiguration, array $variables)
	{
		$moduleClassName    = $moduleConfiguration['className'];
		$module             = new $moduleClassName($this->application);
		$moduleActionName   = 'action' . ucfirst($moduleConfiguration['action']) . ucfirst($moduleConfiguration['mode']);
		return $module->$moduleActionName($variables);
	}
}