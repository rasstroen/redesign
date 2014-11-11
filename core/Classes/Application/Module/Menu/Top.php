<?php

namespace Application\Module\Menu;

use Application\Module\Base;

class Top extends Base
{
	/**
	 * Меню сайта в шапке на главной
	 * @return array
	 */
	public function actionListIndex()
	{
		$selectedFirstLevelItemName   = explode('/', $this->application->request->getUrl());
		$selectedFirstLevelItemName   = isset($selectedFirstLevelItemName[2]) ? $selectedFirstLevelItemName[2] : '';
		$data = array(
			'items' => array(
				'top' => array(
					'title' => 'Рейтинги',
					'url'   => $this->application->routing->getUrl('top'),
				),
				'theme' => array(
					'title' => 'Популярные темы',
					'url'   => $this->application->routing->getUrl('theme'),
				),
			)
		);

		if(isset($data['items'][$selectedFirstLevelItemName]))
		{
			$data['items'][$selectedFirstLevelItemName]['selected'] = true;
		}

		return $data;
	}
}