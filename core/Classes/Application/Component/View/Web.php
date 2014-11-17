<?php
namespace Application\Component\View;
use Application\Component\Base;

class Web extends Base
{
	private $layout;

	private $pageConfiguration;

	public function setConfiguration(array $pageConfiguration)
	{
		$this->pageConfiguration = $pageConfiguration;
	}

	public function getCss()
	{
		return $this->pageConfiguration['css'];
	}

	public function setLayout($layoutName)
	{
		$this->layout = $layoutName;
	}

	private function getLayoutFileName()
	{
		return $this->application->configuration->getRootPath() . 'templates/layouts/' . $this->layout . '.php';
	}

	private function getTemplateFileName(array $moduleConfiguration)
	{
		return $this->application->configuration->getRootPath() . 'templates/modules/' .$moduleConfiguration['template'] . '.php';
	}

	public function renderBlock($blockName, array $modulesData)
	{
		if(isset($modulesData[$blockName]))
		{
			foreach ($modulesData[$blockName] as $moduleData)
			{
				$templateFileName = $this->getTemplateFileName($moduleData['configuration']);
				require_once $templateFileName;
				$templateNameArray = explode(DIRECTORY_SEPARATOR, $moduleData['configuration']['template']);
				$templateString = ucfirst(str_replace(DIRECTORY_SEPARATOR, '',array_pop($templateNameArray)));
				$templateFunction = 'template' . $templateString . ucfirst($moduleData['configuration']['action']) . ucfirst($moduleData['configuration']['mode']);
				$templateFunction($moduleData['data']);
			}
		}
	}

	public function render(array $modulesData)
	{
		$application    = $this->application;
		$view           = $this;
		extract(
			array(
				$modulesData,
				$application,
				$view
			)
		);

		require $this->getLayoutFileName();
	}
}