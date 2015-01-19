<?php

namespace Application\Module;

class Author extends Base
{
	public function actionShowItem(array $variables)
	{
		$author = $this->application->bll->author->getByUserName($variables['username']);

		return array(
			'author' => $author
		);
	}

	public function actionListIndexTop(array $variables)
	{
		$authors = $this->application->bll->author->getTop(
			\Application\BLL\Author::AUTHOR_TYPE_USER,
			1,
			$variables['limit']
		);

		return
			array(
				'authors' => $authors,
				'type'    => \Application\BLL\Author::AUTHOR_TYPE_USER,
			);
	}

	public function actionListTop(array $variables)
	{
		$page = max(1, $this->application->request->getQueryParam('p'));
		if($variables['authorType'] == 'community')
		{
			$authors = $this->application->bll->author->getTop(\Application\BLL\Author::AUTHOR_TYPE_COMMUNITY, $page);
		}
		else
		{
			$authors = $this->application->bll->author->getTop(\Application\BLL\Author::AUTHOR_TYPE_USER, $page);
		}

		return
			array(
				'authors' => $authors,
				'type'    => $variables['authorType']
			);
	}
}
