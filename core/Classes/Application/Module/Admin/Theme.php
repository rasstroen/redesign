<?php

namespace Application\Module\Admin;

use Application\Module\Base;

class Theme extends Base
{
	public function doUpdate()
	{
		$themeId = $this->application->request->getPostParam('themeId' , 0);
		if(!$themeId)
		{
			$themeId = $this->application->bll->theme->add(
				$this->application->request->getPostParam('title'),
				$this->application->request->getPostParam('name'),
				$this->application->request->getPostParam('finish'),
				$this->application->request->getPostParam('description' , '')
			);
			return $this->application->request->redirect(
				$this->application->routing->getUrl('admin/theme/' . $themeId)
			);
		}
	}

	public function actionListAdmin()
	{
		$data = array();
		$data['themes'] = $this->application->bll->theme->getAll();
		$data['addUrl'] = $this->application->routing->getUrl('admin/theme/0');
		return $data;
	}

	public function actionShowItem(array $params)
	{
		$data               = array();
		$themeId            = $params['themeId'];
		$data['themeId']    = $themeId;
		return $data;
	}
}
