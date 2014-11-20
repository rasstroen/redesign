<?php

namespace Application\Module;

class Author extends Base
{
	public function actionListTop()
	{
		$page       = max(1, $this->application->request->getQueryParam('p'));
		$authors    = $this->application->bll->author->getTop(\Application\BLL\Author::AUTHOR_TYPE_USER, $page);
		return
			array(
				'authors' => $authors
			);
	}
}
