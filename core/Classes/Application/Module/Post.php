<?php

namespace Application\Module;

class Post extends Base
{
	const POSTS_POPULAR_ON_MAIN 	= 10;
	const POSTS_NEW_ON_MAIN 		= 10;
	public function actionListIndexTopPopular()
	{
		$posts = $this->application->bll->posts->getPopularPosts(self::POSTS_POPULAR_ON_MAIN);
		$this->application->bll->posts->preparePosts($posts);
		return array(
			'posts' => $posts
		);
	}

	public function actionListIndexTopNew()
	{
		$posts = $this->application->bll->posts->getNewPosts(self::POSTS_NEW_ON_MAIN);
		$this->application->bll->posts->preparePosts($posts);
		return array(
			'posts' => $posts
		);
	}
}
