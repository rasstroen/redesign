<?php

namespace Application;

/**
 * Class Web
 * @package Application
 *
 * @property \Application\Component\Configuration\Base  $configuration
 * @property \Application\Component\Request\Web         $request
 * @property \Application\Component\Routing\Web         $routing
 * @property \Application\Component\Controller\Web      $controller
 * @property \Application\Component\View\Web            $view
 */
class Web extends Base
{
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
		$this->end();
	}

	public function getIsDevelopmentMode()
	{
		return $this->request->getQueryParam('devMode', false);
	}

	public function end()
	{
		exit;
	}
}
