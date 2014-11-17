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
			$rubric['addChildUrl']  = $this->application->routing->getUrl('admin/rubric/' . $rubric['rubric_id'] .'/edit');
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

	public function actionEditItem()
	{
		return array();
	}
}
