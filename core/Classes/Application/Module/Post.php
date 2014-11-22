<?php

namespace Application\Module;

class Post extends Base
{
	public function actionListIndexTopPopular()
	{
		$posts = $this->application->bll->posts->getPopularPosts(32);
		$this->application->bll->posts->preparePosts($posts);
		return array(
			'posts' => $posts
		);
	}
}
