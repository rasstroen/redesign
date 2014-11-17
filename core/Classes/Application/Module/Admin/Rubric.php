<?php

namespace Application\Module\Admin;

use Application\Module\Base;

class Rubric extends Base
{
	public function doEdit()
	{
		$rubricId   = $this->application->request->getPostParam('rubricId' , 0);
		$parentId   = $this->application->request->getPostParam('parentId' , 0);
		$title      = $this->application->request->getPostParam('title' , '');
		$name       = $this->application->request->getPostParam('name' , '');

		if(!$rubricId)
		{
			/**
			 * Добавление
			 */
			$this->application->bll->rubric->addToParent(
				$parentId,
				array(
					'title' => $title,
					'name'  => $name,
				)
			);
		}
		else
		{
			/**
			 * Редактирование
			 */
			$this->application->bll->rubric->edit(
				$rubricId,
				array(
					'title' => $title,
					'name'  => $name,
				)
			);
		}

		$this->application->request->redirect(
			$this->application->routing->getUrl('admin/rubric')
		)->end();
	}
}
