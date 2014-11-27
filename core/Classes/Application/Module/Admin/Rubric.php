<?php

namespace Application\Module\Admin;

use Application\BLL\Queue;
use Application\Module\Base;

class Rubric extends Base
{
	public function doConfirmLink()
	{
		$this->application->bll->rubric->confirmPostLink(
			$this->application->request->getPostParam('postId' , 0),
			$this->application->request->getPostParam('authorId' , 0),
			$this->application->request->getPostParam('pubDate' , 0),
			$this->application->request->getPostParam('rubricId' , 0)
		);
	}

	public function doDeleteLink()
	{
		$this->application->bll->rubric->deletePostLink(
			$this->application->request->getPostParam('postId' , 0),
			$this->application->request->getPostParam('authorId' , 0),
			$this->application->request->getPostParam('pubDate' , 0),
			$this->application->request->getPostParam('rubricId' , 0)
		);
	}

	public function doRecalc()
	{
		$this->application->bll->queue->addTask(
			Queue::QUEUE_POSTS_PROCESS_RECALCULATE_RUBRICS
		);
	}

	public function doDelWord()
	{
		$phraseId   = $this->application->request->getPostParam('phraseId' , 0);
		if($phraseId)
		{
			$this->application->bll->rubric->delPhrase($phraseId);
		}
	}
	public function doAddWord()
	{
		$rubricId   = $this->application->request->getPostParam('rubricId' , 0);
		$this->application->bll->rubric->addPhrase(
			$rubricId,
			$this->application->request->getPostParam('name' , '')
		);

	}
	public function doDelete()
	{
		$rubricId   = $this->application->request->getQueryParam('rubricId' , 0);
		$rubric     = $this->application->bll->rubric->getById($rubricId);
		if($rubric && $rubric['deleted'])
		{
			$this->application->bll->rubric->deleteWithChilds($rubricId);
		}
		elseif($rubricId)
		{
			$this->application->bll->rubric->setHiddenWithChilds($rubricId);
		}
		$this->application->request->redirect(
			$this->application->routing->getUrl('admin/rubric')
		)->end();
	}

	public function doRestore()
	{
		$rubricId   = $this->application->request->getQueryParam('rubricId' , 0);
		if($rubricId)
		{
			$this->application->bll->rubric->restore($rubricId);
			$this->application->request->redirect(
				$this->application->routing->getUrl('admin/rubric')
			)->end();
		}
	}

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
