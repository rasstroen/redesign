<?php

namespace Application\Module\Menu;

use Application\Module\Base;

class Top extends Base
{
	/**
	 * Меню админки
	 */
	public function actionListAdmin()
	{
		$selectedFirstLevelItemName   = explode('/', $this->application->request->getUrl());
		$selectedFirstLevelItemName   = isset($selectedFirstLevelItemName[2]) ? $selectedFirstLevelItemName[2] : '';
		$data = array(
			'items' => array(
				'rubric' => array(
					'title' => 'Рубрикатор',
					'url'   => $this->application->routing->getUrl('admin/rubric'),
				),
				'theme' => array(
					'title' => 'Популярные темы',
					'url'   => $this->application->routing->getUrl('admin/theme'),
				),
				'demons' => array(
					'title' => 'Демоны',
					'url'   => $this->application->routing->getUrl('admin/demons'),
				),
			)
		);

		if(isset($data['items'][$selectedFirstLevelItemName]))
		{
			$data['items'][$selectedFirstLevelItemName]['selected'] = true;
		}
		return $data;
	}

	/**
	 * Меню сайта в шапке на главной
	 * @return array
	 */
	public function actionListIndex()
	{
		$selectedFirstLevelItemName   = explode('/', $this->application->request->getUrl());
		$selectedFirstLevelItemName   = isset($selectedFirstLevelItemName[1]) ? $selectedFirstLevelItemName[1] : '';
		$data = array(
			'items' => array(
				'' => array(
					'title' => 'Главная',
					'url'   => $this->application->routing->getUrl(''),
				),
				'top' => array(
					'title' => 'Рейтинг авторов',
					'url'   => $this->application->routing->getUrl('top/month/authors'),
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