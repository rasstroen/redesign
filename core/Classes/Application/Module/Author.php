<?php

namespace Application\Module;

class Author extends Base
{
	public function actionListTop(array $variables)
	{
		$page       = max(1, $this->application->request->getQueryParam('p'));
		if($variables['authorType'] == 'community')
		{
			$authors    = $this->application->bll->author->getTop(\Application\BLL\Author::AUTHOR_TYPE_COMMUNITY, $page);
		}
		else
		{
			$authors    = $this->application->bll->author->getTop(\Application\BLL\Author::AUTHOR_TYPE_USER, $page);
		}

		return
			array(
				'authors'   => $authors,
				'type'      => $variables['authorType']
			);
	}
}
