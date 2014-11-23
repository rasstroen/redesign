<?php
namespace Application\Command\Tasks\Worker;

use Application\BLL\Queue;

class Post extends Base
{
	private $minPostsComments   = 30;

	public function processImages($data)
	{
		$this->log('PROCESS IMAGES');
		$this->log(print_r($data,1));
		die();
	}
	/**
	 * @param $posts
	 */
	public function methodProcess($data)
	{
		$username = $data['username'];
		$this->log('saving ' . count($data['posts']) . ' posts, author: ' . $username);
		$author = $this->application->bll->author->getByUserName($username);
		if(!$author)
		{
			$this->log('no author: ' . $username);
			return;
		}
		foreach($data['posts'] as $post)
		{
			if($post['comments'] > $this->minPostsComments)
			{
				$this->application->bll->posts->saveAuthorPost(
					$author['author_id'],
					$post
				);
			}

			$this>log('Addinf task to process post image: ' . $post['url']);
			$this->application->bll->queue->addTask(
				Queue::QUEUE_POSTS_PROCESS_POSTS_IMAGES,
				$post['url'],
				$post
			);
			/**
			 * [24] => Array
			(
			[url] => http://duzer007.livejournal.com/1606741.html
			[guid] => http://duzer007.livejournal.com/1606741.html
			[pubdate] => Thu, 13 Nov 2014 14:17:18 GMT
			[title] => А я с этим живу.
			[link] => http://duzer007.livejournal.com/1606741.html
			[description] => Итак, у меня во френдах имеются:тульский ватник, алкоголики из Москвы, имперский выскочка, лиса, одесский укроп, еноты, хуй какой-то и жена евоная, мох, сибаритки и богодулка (в одном лице), теребонька, и это только начало списка...<a name='cutid1-end'></a>
			[comments] => 96
			[tags] => Array
			(
				[френды] => френды
				[подумалось] => подумалось
				[знайнаших] => знайнаших
				[ЖЖ] => ЖЖ
				[дети-радиации] => дети-радиации
			)

			)

			 */
		}
	}
}
