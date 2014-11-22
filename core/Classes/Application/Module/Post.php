<?php

namespace Application\Module;

class Post extends Base
{
	const POSTS_POPULA_ON_MAIN = 33;
	public function actionListIndexTopPopular()
	{
		$posts = $this->application->bll->posts->getPopularPosts(self::POSTS_POPULA_ON_MAIN);
		$this->application->bll->posts->preparePosts($posts);
		return array(
			'posts' => $posts
		);
	}
}
