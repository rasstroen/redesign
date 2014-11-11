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
	}

	public function getIsDevelopmentMode()
	{
		return $this->request->getQueryParam('devMode', false);
	}
}