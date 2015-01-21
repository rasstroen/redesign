<?php

namespace Application\Module;

class Post extends Base
{
	const POSTS_POPULAR_ON_MAIN 	= 12;
	const POSTS_NEW_ON_MAIN 		= 3;

	public function actionShowItem(array $variables)
	{
		$username   = $variables['username'];
		$postId     = $variables['postId'];

		$author     = $this->application->bll->author->getByUserName($username);
		if(!$author)
		{
			$author     = reset($this->application->bll->author->getByIds(array($username)));
		}
		$post       = $this->application->bll->posts->getPostByAuthorIdPostId($postId, $author['author_id']);
		$posts      = array($post);
		$this->application->bll->posts->preparePosts($posts);
		return array(
			'post'  => reset($posts)
		);
	}
	public function actionListIndexTopPopular(array $variables)
	{
		$limit = isset($variables['limit']) ? $variables['limit'] : self::POSTS_POPULAR_ON_MAIN;
		$offset = isset($variables['offset']) ? $variables['offset'] : 0;
		$posts = $this->application->bll->posts->getPopularPosts($limit, $offset);
		$this->application->bll->posts->preparePosts($posts);
		return array(
			'posts' => $posts
		);
	}

	public function actionListIndexTopRead(array $variables)
	{
		$limit = isset($variables['limit']) ? $variables['limit'] : self::POSTS_POPULAR_ON_MAIN;
		$offset = isset($variables['offset']) ? $variables['offset'] : 0;
		$posts = $this->application->bll->posts->getPopularPosts($limit, $offset);
		$this->application->bll->posts->preparePosts($posts);
		return array(
			'posts' => $posts
		);
	}

	public function actionListIndexTopCommented(array $variables)
	{
		$limit = isset($variables['limit']) ? $variables['limit'] : self::POSTS_POPULAR_ON_MAIN;
		$offset = isset($variables['offset']) ? $variables['offset'] : 0;
		$posts = $this->application->bll->posts->getPopularPosts($limit, $offset);
		$this->application->bll->posts->preparePosts($posts);
		return array(
			'posts' => $posts
		);
	}

	public function actionListIndexTopNew(array $variables)
	{
		$limit = isset($variables['limit']) ? $variables['limit'] : self::POSTS_NEW_ON_MAIN;
		$offset = isset($variables['offset']) ? $variables['offset'] : 0;
		$posts = $this->application->bll->posts->getNewPosts($limit, $offset);
		$this->application->bll->posts->preparePosts($posts);
		return array(
			'posts' => $posts
		);
	}
}
