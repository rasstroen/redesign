<?php

namespace Application\Module\Admin;

use Application\Module\Base;

class Theme extends Base
{
	public function doAdd_phrase()
	{
		$themeId    = $this->application->request->getPostParam('themeId' , 0);
		$phrase     = $this->application->request->getPostParam('phrase');
		if($phrase && $themeId)
		{
			$this->application->bll->theme->addPhrase($themeId, $phrase);
		}
		return $this->application->request->redirect(
			$this->application->routing->getUrl('admin/theme/' . $themeId)
		);
	}

	public function doDelete()
	{
		$themeId    = $this->application->request->getQueryParam('themeId' , 0);
		$phrase     = $this->application->request->getQueryParam('phrase');
		if($phrase && $themeId)
		{
			$this->application->bll->theme->delPhrase($themeId, $phrase);
		}
	}


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
		else
		{
			$this->application->bll->theme->updateById(
				$themeId,
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
		$this->application->bll->theme->prepareThemesPhrases($data['themes']);

		foreach($data['themes'] as &$theme)
		{
			$theme['editUrl'] = $this->application->routing->getUrl('admin/theme/' . $theme['theme_id']);
		}
		unset($theme);
		$data['addUrl'] = $this->application->routing->getUrl('admin/theme/0');
		return $data;
	}

	public function actionShowItem(array $params)
	{
		$data               = array();
		$themeId            = $params['themeId'];
		if($themeId)
		{
			$data['themes'][$themeId] = $this->application->bll->theme->getById($themeId);
			$this->application->bll->theme->prepareThemesPhrases($data['themes']);
		}
		$data['themeId']    = $themeId;
		return $data;
	}
}
