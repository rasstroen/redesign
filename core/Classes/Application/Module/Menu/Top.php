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

	public function actionListIndexSearch()
	{
		return array();
	}

	/**
	 * Меню сайта в шапке на главной
	 * @return array
	 */
	public function actionListIndex()
	{
		$selected                       = explode('/', $this->application->request->getUrl());
		$selectedFirstLevelItemName     = isset($selected[1]) ? $selected[1] : '';
		$selectedSecondLevelItemName    = isset($selected[2]) ? $selected[2] : '';
		$data = array(
			'items' => array(
				'popular' => array(
					'title' => 'Записи',
					'items' => array(
						'authors'   => array(
							'title' => 'Популярные',
							'url'   => $this->application->routing->getUrl('popular'),
						),
						'community' => array(
							'title' => 'Свежие',
							'url'   => $this->application->routing->getUrl('newest'),
						),
					)
				),
				'theme' => array(
					'title' => 'Темы',
					'url'   => $this->application->routing->getUrl('theme'),
				),
				'top' => array(
					'title' => 'Рейтинги',
					'items' => array(
						'authors'   => array(
							'title' => 'Рейтинг авторов',
							'url'   => $this->application->routing->getUrl('top/authors'),
						),
						'community' => array(
							'title' => 'Рейтинг сообществ',
							'url'   => $this->application->routing->getUrl('top/community'),
						),
					)
				),
				'video' => array(
					'title' => 'Видео',
					'url'   => $this->application->routing->getUrl('video'),
				),
				'public' => array(
					'title' => 'Паблики',
					'url'   => $this->application->routing->getUrl('public'),
				),
			)
		);
		if(isset($data['items'][$selectedFirstLevelItemName]))
		{
			$data['items'][$selectedFirstLevelItemName]['selected'] = true;
			if(isset($data['items'][$selectedFirstLevelItemName]['items'][$selectedSecondLevelItemName]))
			{
				$data['items'][$selectedFirstLevelItemName]['items'][$selectedSecondLevelItemName]['selected'] = true;

			}
		}

		$data['rubrics'] = $this->application->bll->rubric->getTop(7);
		foreach($data['rubrics'] as &$rubric)
		{
			$rubric['url'] = '/rubric/' . $rubric['name'];
		}
		unset($rubric);

		return $data;
	}

	public function actionListFooter()
	{
		return array();
	}
}