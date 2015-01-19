<?php

namespace Application\Module;

class Rubric extends Base
{

	public function actionListIndex()
	{
		$postsCountInRubric = 3;
		$postsLinks         = $this->application->bll->rubric->getActiveLinkedPosts();
		$rubricIds          = array();
		foreach($postsLinks as $postLink)
		{
			$rubricIds[$postLink['rubric_id']]      = $postLink['rubric_id'];
			$postsByRubric[$postLink['rubric_id']][$postLink['post_id'].'-'.$postLink['author_id']]  = $postLink;
		}
		if(!$rubricIds)
		{
			return array(
				'rubrics'   => array(),
				'posts'     => array()
			);
		}
		$rubrics    = $this->application->bll->rubric->getByIds($rubricIds);
		$toFetch = array();
		foreach($rubrics as $rubric)
		{
			if(!isset($rubrics[$rubric['rubric_id']]['posts']))
			{
				$rubrics[$rubric['rubric_id']]['posts'] = array();
			}
			if(count($rubrics[$rubric['rubric_id']]['posts']) < $postsCountInRubric)
			{
				$rubrics[$rubric['rubric_id']]['posts'] = $postsByRubric[$rubric['rubric_id']];
				$toFetch += $postsByRubric[$rubric['rubric_id']];
			}
		}
		$posts = $this->application->bll->posts->getPostsByIds($toFetch);
		$this->application->bll->posts->preparePosts($posts);
		$data = array(
			'rubrics'   => $rubrics,
			'posts'     => $posts
		);
		return $data;
	}

	public function actionListAdminRubrics()
	{
		$rubrics        = $this->application->bll->rubric->getAll();
		$countsUnlinked = $this->application->bll->rubric->getPostsCountsUnlinked(array_keys($rubrics));
		$countsActive   = $this->application->bll->rubric->getPostsCountsActive(array_keys($rubrics));

		foreach($rubrics as &$rubric)
		{
			$rubric['adminUrl']     = $this->application->routing->getUrl('admin/rubric/' . $rubric['rubric_id']);
			$rubric['addUrl']       = $this->application->routing->getUrl('admin/rubric/0/edit?parentId=' . $rubric['rubric_id']);
			$rubric['editUrl']      = $this->application->routing->getUrl('admin/rubric/' . $rubric['rubric_id'] .'/edit');
			$rubric['linkedUrl']    = $this->application->routing->getUrl('admin/rubric/' . $rubric['rubric_id'] .'/linked');
			$rubric['deleteUrl']    = $this->application->routing->getUrl('admin/rubric/?writemodule=admin/rubric&method=delete&rubricId=' . $rubric['rubric_id']);
			$rubric['restoreUrl']   = $this->application->routing->getUrl('admin/rubric/?writemodule=admin/rubric&method=restore&rubricId=' . $rubric['rubric_id']);
			$rubric['posts_count_unlinked']  = isset($countsUnlinked[$rubric['rubric_id']]) ? intval
			($countsUnlinked[$rubric['rubric_id']]['cnt']) : 0;
			$rubric['posts_count_active']  = isset($countsActive[$rubric['rubric_id']]) ? intval
			($countsActive[$rubric['rubric_id']]['cnt']) : 0;
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

		if(!$isDone)
		{
			$data['posts']      = $this->application->bll->rubric->getRubricAutoLinkedPosts($rubricId);
		}
		else
		{
			$data['posts']      = $this->application->bll->rubric->getRubricLinkedPosts($rubricId);
		}
		$data['isDone']     = $isDone;
		$data['rubric']     = $rubric;
		$data['notDoneUrl'] = $this->application->routing->getUrl('admin/rubric/' . $rubricId .'/linked');
		$data['doneUrl']    = $this->application->routing->getUrl('admin/rubric/' . $rubricId .'/linked/done');
		uasort($data['posts'], function ($post1, $post2)
		{
			if(!isset($post2['phrases']))
			{
				return $post1['pub_time'] - $post2['pub_time'];
			}
			else
			{
				return count($post2['phrases']) - count($post1['phrases']);
			}
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
