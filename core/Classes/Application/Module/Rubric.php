<?php

namespace Application\Module;

class Rubric extends Base
{
	public function actionListAdminRubrics()
	{
		$rubrics    = $this->application->bll->rubric->getAll();
		foreach($rubrics as &$rubric)
		{
			$rubric['adminUrl']     = $this->application->routing->getUrl('admin/rubric/' . $rubric['rubric_id']);
			$rubric['addUrl']       = $this->application->routing->getUrl('admin/rubric/0/edit?parentId=' . $rubric['rubric_id']);
			$rubric['editUrl']      = $this->application->routing->getUrl('admin/rubric/' . $rubric['rubric_id'] .'/edit');
			$rubric['linkedUrl']    = $this->application->routing->getUrl('admin/rubric/' . $rubric['rubric_id'] .'/linked');
			$rubric['deleteUrl']    = $this->application->routing->getUrl('admin/rubric/?writemodule=admin/rubric&method=delete&rubricId=' . $rubric['rubric_id']);
			$rubric['restoreUrl']   = $this->application->routing->getUrl('admin/rubric/?writemodule=admin/rubric&method=restore&rubricId=' . $rubric['rubric_id']);
			$rubric['posts_count']  = 0;
		}
		unset($rubric);
		$parents    = array();
		foreach($rubrics as $rubric)
		{
			$parents[$rubric['parent_id']][$rubric['rubric_id']] = $rubric;
		}

		return array(
			'rubricsByParents'  => $parents,
			'addUrl'            => $this->application->routing->getUrl('admin/rubric/0/edit?parentId=0'),
		);
	}

	public function actionShowItem(array $variables = array())
	{

		$rubricId   =  $variables['rubricId'];
		if($rubricId)
		{
			$rubric = $this->application->bll->rubric->getById($rubricId);
		}
		else
		{
			$rubric = null;
		}

		if($rubric)
		{
			$parentRubric = $this->application->bll->rubric->getById($rubric['parent_id']);
			if($parentRubric)
			{
				$parentRubric['adminUrl'] = $this->application->routing->getUrl('admin/rubric/' . $parentRubric['rubric_id']);
			}
		}
		else
		{
			$parentRubric = null;
		}
		return array(
			'rubric'        => $rubric,
			'parentRubric'  => $parentRubric
		);
	}

	public function actionListAdminLinkedPosts(array $variables)
	{
		$isDone     = isset($variables['isDone']) && $variables['isDone'] == 'done';
		$rubricId   = $variables['rubricId'];
		$rubric     = $this->application->bll->rubric->getById($rubricId);
		$counts     = $this->application->bll->rubric->getPhrasesCountsByRubricId($rubricId);

		$data['posts']      = $this->application->bll->rubric->getRubricAutoLinkedPosts($rubricId);
		$data['isDone']     = $isDone;
		$data['counts']     = $counts;
		$data['rubric']     = $rubric;
		$data['notDoneUrl'] = $this->application->routing->getUrl('admin/rubric/' . $rubricId .'/linked');
		$data['doneUrl']    = $this->application->routing->getUrl('admin/rubric/' . $rubricId .'/linked/done');
		uasort($data['posts'], function ($post1, $post2){
			return count($post2['phrases']) - count($post1['phrases']);
		});
		return $data;
	}


	public function actionEditItem(array $variables = array())
	{

		$parentId   =  $this->application->request->getQueryParam('parentId' , 0);
		$rubricId   =  $variables['rubricId'];
		$phrases    = array();
		if($rubricId)
		{
			$rubric                 = $this->application->bll->rubric->getById($rubricId);
			$rubric['linkedUrl']    = $this->application->routing->getUrl('admin/rubric/' . $rubric['rubric_id'] .'/linked');
			$phrases                = $this->application->bll->rubric->getPhrasesCountsByRubricId($rubricId);
		}
		else
		{
			$rubric = null;
		}

		if($parentId)
		{
			$parentRubric = $this->application->bll->rubric->getById($parentId);
		}
		else
		{
			$parentRubric = null;
		}
		return array(
			'parentId'      => $parentId,
			'rubric'        => $rubric,
			'parentRubric'  => $parentRubric,
			'phrases'       => $phrases
		);
	}
}
