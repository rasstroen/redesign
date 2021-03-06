<?php
namespace Application\Command\Tasks\Worker;

use Application\BLL\Posts;
use Application\BLL\Video;

class Post extends Base
{
	private $minSizeX = 340;
	private $minSizeY = 200;
	private $minPostsComments = 30;
	private $wideSize = 699;

	private $settings = array(
		'small'  => array(
			'crop_method'      => 1,
			'width_requested'  => 100,
			'height_requested' => 100,
		),
		'normal' => array(
			'crop_method'      => 1,
			'width_requested'  => 150,
			'height_requested' => 150,
		),
		'big'    => array(
			'crop_method'      => 0,
			'width_requested'  => 340,
			'height_requested' => 200,
		),
		'wide'   => array(
			'crop_method'      => 0,
			'width_requested'  => 700,
			'height_requested' => 250,
		),
	);

	public function methodProcessVideosThumbs($data)
	{

		foreach($data as $video)
		{
			try
			{
				$videoObject =
					$this->application->youtube->getByUrl('http://www.youtube.com/watch?v=' . $video['video_id']);
				$url         = $videoObject->getYoutubeLink();
				$thumbnails  = $videoObject->getThumbnails();
			}catch (\Exception $e)
			{
				$this->log('error: ' . print_r($e));
				$thumbnails = false;
			}
			if($thumbnails)
			{
				$this->saveVideoThumb(
					$video['post_id'],
					$video['author_id'],
					$video['video_id'],
					$thumbnails['maxres'] ? $thumbnails['maxres'] : $thumbnails['high']
				);
				$this->application->bll->video->update(
					$video['post_id'],
					$video['author_id'],
					$video['video_id'],
					$video['date'],
					$url,
					Video::HAS_THUMB_YES
				);
			}
			else
			{
				$this->application->bll->video->update(
					$video['post_id'],
					$video['author_id'],
					$video['video_id'],
					$video['date'],
					'-',
					Video::HAS_THUMB_NO
				);
			}
		}
	}

	public function saveVideoThumb($postId, $authorId, $videoId, $url)
	{
		$hasPic = 0;
		$res    = array();
		$temp   = '/tmp/images/v-' . md5(rand(12, 101122332)) . time() . microtime(true) . '.jpg';
		file_put_contents($temp, file_get_contents($url));

		$size = getimagesize($temp);

		if($size)
		{
			if(($size[0] > $this->minSizeX) && ($size[1] > $this->minSizeY))
			{
				$hasPic = Posts::PIC_STATUS_HAS_PIC;
				if($size[0] >= $this->wideSize)
				{
					$hasPic = Posts::PIC_STATUS_HAS_WIDE_PIC;
					$res[]  =
						$this->application->imageConverter->resize(
							$temp,
							$this->settings['wide'],
							$this->getLocalVideoPathWide($postId, $authorId, $videoId),
							$size
						);
				}
				$res[] =
					$this->application->imageConverter->resize(
						$temp,
						$this->settings['normal'],
						$this->getLocalVideoPath($postId, $authorId, $videoId),
						$size
					);
				$res[] =
					$this->application->imageConverter->resize(
						$temp,
						$this->settings['small'],
						$this->getLocalVideoPathSmall($postId, $authorId, $videoId),
						$size
					);
				$res[] =
					$this->application->imageConverter->resize(
						$temp,
						$this->settings['big'],
						$this->getLocalVideoPathBig($postId, $authorId, $videoId),
						$size
					);
			}
		}
		@unlink($temp);
		$this->log(print_r($res, 1));

		return $hasPic;
	}

	public function methodProcessVideos($data)
	{
		$this->log('processing videos');
		$post = reset($this->application->bll->posts->getPostsByIds(array($data)));
		$text = $post['text'];

		preg_match_all('/embed\s+id=\"(.+)\"/isU', $text, $embeds);


		if(isset($embeds[1][0]))
		{
			$this->log('found videos: ' . $post['post_id'] . '-' . $post['author_id'] . ' ' . print_r($embeds[1], 1));
			$videos = $this->getRawVideos($post, $embeds);
			if(count($videos))
			{
				$this->log('saving videos: ' . print_r($videos, 1));
				$this->application->bll->video->savePostYoutubeVideos($post, $videos);
				$this->application->bll->posts->setHasVideo(
					$post['post_id'],
					$post['author_id'],
					Posts::VIDEO_STATUS_HAS_PIC
				);
			}
		}
		else
		{
			$this->log('video not found in db text for post ' . $post['post_id'] . '-' . $post['author_id']);
			$this->application->bll->posts->setHasVideo(
				$post['post_id'],
				$post['author_id'],
				Posts::VIDEO_STATUS_HASNOT_PIC
			);
		}
	}

	public function getRawVideos(array $post, $embedIds)
	{
		$videos = array();
		$author = reset(
			$this->application->bll->author->getByIds(
				array($post['author_id'])
			)
		);

		$postUrl = 'http://' . $author['username'] . '.livejournal.com/' . $post['post_id'] . '.html';
		$this->log('getting url: ' . $postUrl);
		$rawText = $this->application->httpRequest->getWithCache(
			$postUrl,
			60 * 60,
			20
		);

		preg_match_all('/iframe src=\"(.*)\".*name="embed_(\d+)_(\d+)"/isU', $rawText, $iframes);
		if(count($iframes))
		{
			foreach($iframes[1] as $index => $url)
			{
				$params        = array();
				$urlData       = parse_url($url);
				$paramsStrings = explode('&amp;', $urlData['query']);
				foreach($paramsStrings as $string)
				{
					$value             = explode('=', $string);
					$params[$value[0]] = $value[1];
				}
				if(!empty($params['source']) && $params['source'] == 'youtube')
				{
					if($params['vid'])
					{
						$this->log('youtube video vid=' . $params['vid']);
						$videos[] = array('id' => $params['vid'], 'embed_id' => $iframes[3][$index]);
					}
				}
				else
				{
					$this->log('unknown video type:' . print_r($params, 1));
				}
			}
		}

		return $videos;
	}

	public function methodProcessImages($data)
	{
		$this->log('processing images');

		if(isset($data['url']))
		{
			$username    = explode('/', $data['url']);
			$postId      = intval(array_pop($username));
			$username    = str_replace('-', '_', $username[2]);
			$username    = reset(explode('.', $username));
			$author      = $this->application->bll->author->getByUserName($username);
			$authorId    = $author['author_id'];
			$description = $data['description'];
		}
		else
		{
			$authorId = $data['author_id'];
			$postId   = $data['post_id'];
			try
			{
				$post = $this->application->bll->posts->getPostByAuthorIdPostId($postId, $authorId);
			}
			catch(\Exception $e)
			{
				$this->log(print_r($e, 1) . ' ' . $authorId . ' , ' . $postId);
			}
			$description = $post['text'];
		}

		preg_match_all("/(<img )(.+?)( \/)?(>)/", $description, $images);
		$urls = array();
		foreach($images[2] as $val)
		{
			if(preg_match("/(src=)('|\")(.+?)('|\")/", $val, $matches) == 1)
			{
				$urls[$matches[3]] = $matches[3];
			}
		}

		if(!count($urls))
		{
			$this->log('no pictures post' . $postId);
			$this->application->bll->posts->setHasPic($postId, $authorId, $hasPic = Posts::PIC_STATUS_HASNOT_PIC);

			return;
		}

		mt_rand(1, 21312323);
		$temp = '/tmp/images/' . md5(rand(12, 101122332)) . time() . microtime(true) . '.jpg';
		$this->log('pic saving to temp: ' . $temp . ' postId=' . $postId . ' authorId=' . $authorId);
		$hasPic = Posts::PIC_STATUS_HASNOT_PIC;

		foreach($urls as $picUrl)
		{
			try
			{
				$res = array();
				file_put_contents($temp, file_get_contents($picUrl));
				$size = getimagesize($temp);
				$f = fopen('/tmp/sizes', 'a');
				$s = $size[0]. "\t" . $size[1];
				$this->log('image sizes: '. $s . ' | url = ' . $picUrl);
				fwrite($f, $s . "\n");
				fclose($f);
				if($size)
				{
					if(($size[0] > $this->minSizeX) && ($size[1] > $this->minSizeY))
					{
						$hasPic = Posts::PIC_STATUS_HAS_PIC;
						if($size[0] >= $this->wideSize)
						{
							$hasPic = Posts::PIC_STATUS_HAS_WIDE_PIC;
							$res[]  =
								$this->application->imageConverter->resize(
									$temp,
									$this->settings['wide'],
									$this->getLocalImagePathWide($postId, $authorId),
									$size
								);
						}
						$this->log('resizing images for post' . $postId);
						$res[] =
							$this->application->imageConverter->resize(
								$temp,
								$this->settings['normal'],
								$this->getLocalImagePath($postId, $authorId),
								$size
							);
						$res[] =
							$this->application->imageConverter->resize(
								$temp,
								$this->settings['small'],
								$this->getLocalImagePathSmall($postId, $authorId),
								$size
							);
						$res[] =
							$this->application->imageConverter->resize(
								$temp,
								$this->settings['big'],
								$this->getLocalImagePathBig($postId, $authorId),
								$size
							);
						break;
					}
					else $this->log('too small pic post' . $postId . ' sizes ' . print_r($size, 1));
				}
			}
			catch(\Exception $e)
			{
				$hasPic = Posts::PIC_STATUS_HASNOT_PIC;
				$this->log('Exc:' . $e->getMessage());
			}
			@unlink($temp);
		}
		$this->log(print_r($res, 1));

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

	/**
	 * @param $postId
	 * @param $authorId
	 *
	 * @return string
	 */
	function getLocalVideoPath($postId, $authorId, $videoId)
	{
		return $this->getLocalVideoFolder($postId, $authorId, $videoId) . $videoId . '.jpg';
	}

	function getLocalVideoPathSmall($postId, $authorId, $videoId)
	{
		return $this->getLocalVideoFolder($postId, $authorId, $videoId) . $videoId . '_s.jpg';
	}

	function getLocalVideoPathBig($postId, $authorId, $videoId)
	{
		return $this->getLocalVideoFolder($postId, $authorId, $videoId) . $videoId . '_b.jpg';
	}

	function getLocalVideoPathWide($postId, $authorId, $videoId)
	{
		return $this->getLocalVideoFolder($postId, $authorId, $videoId) . $videoId . '_w.jpg';
	}


	function getLocalImageFolder($postId, $authorId)
	{
		return '/home/sites/lj-top.ru/static/pstmgs/' . ($postId % 20) . '/' . ($authorId % 20) . '/';
	}

	function getLocalVideoFolder($postId, $authorId, $videoId)
	{
		return '/home/sites/lj-top.ru/static/pstmgs/v' . substr(md5($videoId),0,
		                                                        6) . ($postId % 20) . '/' . ($authorId % 20) . '/';
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
			/**
			 * [24] => Array
			 * (
			 * [url] => http://duzer007.livejournal.com/1606741.html
			 * [guid] => http://duzer007.livejournal.com/1606741.html
			 * [pubdate] => Thu, 13 Nov 2014 14:17:18 GMT
			 * [title] => А я с этим живу.
			 * [link] => http://duzer007.livejournal.com/1606741.html
			 * [description] => Итак, у меня во френдах имеются:тульский ватник, алкоголики из Москвы, имперский выскочка, лиса, одесский укроп, еноты, хуй какой-то и жена евоная, мох, сибаритки и богодулка (в одном лице), теребонька, и это только начало списка...<a name='cutid1-end'></a>
			 * [comments] => 96
			 * [tags] => Array
			 * (
			 * [френды] => френды
			 * [подумалось] => подумалось
			 * [знайнаших] => знайнаших
			 * [ЖЖ] => ЖЖ
			 * [дети-радиации] => дети-радиации
			 * )
			 *
			 * )

			 */
		}
	}
}
