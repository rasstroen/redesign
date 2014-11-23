<?php
namespace Application\Command\Tasks\Worker;

use Application\BLL\Posts;
use Application\BLL\Queue;

class Post extends Base
{
	private $minSizeX	= 200;
	private $minSizeY	= 200;
	private $minPostsComments   = 30;
	private $wideSize = 700;

	private $settings = array(
		'small' => array(
			'crop_method' 		=> 1,
			'width_requested' 	=> 90,
			'height_requested' 	=> 90,
		),
		'normal' => array(
			'crop_method' 		=> 1,
			'width_requested' 	=> 150,
			'height_requested' 	=> 150,
		),
		'big' => array(
			'crop_method' 		=> 1,
			'width_requested' 	=> 300,
			'height_requested' 	=> 300,
		),
		'wide' => array(
			'crop_method' 		=> 0,
			'width_requested' 	=> 700,
			'height_requested' 	=> 150,
		),
	);

	public function methodProcessImages($data)
	{
		$this->log('processing images');

		if(isset($data['url'])) {
			$username = explode('/', $data['url']);
			$postId   = intval(array_pop($username));
			$username = str_replace('-', '_', $username[2]);
			$username = reset(explode('.', $username));
			$author   = $this->application->bll->author->getByUserName($username);
			$authorId = $author['author_id'];
			$description = $data['description'];
		}
		else
		{
			$authorId 	= $data['author_id'];
			$postId		= $data['post_id'];
			$post		= $this->application->bll->posts->getPostByAuthorIdPostId($postId, $authorId);
			$description	= $post['text'];
		}

		preg_match_all("/(<img )(.+?)( \/)?(>)/", $description, $images);
		$urls = array();
		foreach ($images[2] as $val)
		{
			if (preg_match("/(src=)('|\")(.+?)('|\")/", $val, $matches) == 1)
			{
				$urls[$matches[3]] = $matches[3];
			}
		}

		if (!count($urls))
		{
			$this->log('no pictures');
			return;
		}


		$temp = '/tmp/' . md5(rand(12,10112)) . '.jpg';
		$hasPic = Posts::PIC_STATUS_HASNOT_PIC;

		foreach($urls as $picUrl)
		{
			try {
				$res = array();
				file_put_contents($temp, file_get_contents($picUrl));
				$size = getimagesize($temp);
				if ($size) {
					if (($size[0] > $this->minSizeX) && ($size[1] > $this->minSizeY)) {
						$hasPic = Posts::PIC_STATUS_HAS_PIC;
						if ($size[0] >= $this->wideSize) {
							$hasPic = Posts::PIC_STATUS_HAS_WIDE_PIC;
							$res[]  = $this->application->imageConverter->resize($temp, $this->settings['wide'], $this->getLocalImagePathWide($postId, $authorId), $size);
						}
						$res[] = $this->application->imageConverter->resize($temp, $this->settings['normal'], $this->getLocalImagePath($postId, $authorId), $size);
						$res[] = $this->application->imageConverter->resize($temp, $this->settings['small'], $this->getLocalImagePathSmall($postId, $authorId), $size);
						$res[] = $this->application->imageConverter->resize($temp, $this->settings['big'], $this->getLocalImagePathBig($postId, $authorId), $size);
						break;
					}
				}
			}catch(\Exception $e)
			{
				$this->log('Exc:'. $e->getMessage());
			}
		}
		$this->log(print_r($res,1));
		$this->application->bll->posts->setHasPic($postId, $authorId, $hasPic);
	}

	function getLocalImagePath($postId, $authorId)
	{
		return $this->getLocalImageFolder($postId, $authorId) . $postId . '.jpg';
	}

	function getLocalImagePathSmall($postId, $authorId)
	{
		return $this->getLocalImageFolder($postId, $authorId) . $postId . '_s.jpg';
	}

	function getLocalImagePathBig($postId, $authorId)
	{
		return $this->getLocalImageFolder($postId, $authorId) . $postId . '_b.jpg';
	}

	function getLocalImagePathWide($postId, $authorId)
	{
		return $this->getLocalImageFolder($postId, $authorId) . $postId . '_w.jpg';
	}


	function getLocalImageFolder($postId, $authorId) {
		return '/home/sites/lj-top.ru/static/pstmgs/' . ($postId % 20) . '/' . ($authorId % 20) . '/';
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

				$this->log('Adding task to process post image: ' . $post['url']);
				$this->application->bll->queue->addTask(
					Queue::QUEUE_POSTS_PROCESS_POSTS_IMAGES,
					$post['url'],
					$post
				);
			}
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
