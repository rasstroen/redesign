<?php
namespace Application\BLL;

/**
 * Class Video
 * @package Application\BLL
 */
class Video extends BLL
{
	const VIDEO_TYPE_YOUTUBE = 1;
	const HAS_THUMB_UNKNOWN = 0;
	const HAS_THUMB_NO = 2;
	const HAS_THUMB_YES = 1;

	public function update($postId, $authorId, $videoId, $date, $url, $hasThumb)
	{
		$this->getDbMaster()->query('UPDATE post_videos_' . $date .'
		set has_thumbnail=?,
		video_url=?
		WHERE
		post_id=? AND author_id=? AND video_id=?', array(
				$hasThumb,
				$url,
				$postId,
				$authorId,
				$videoId
			));
	}
	public function getWithUnknownThumbs($limit = 10)
	{
		$date     = date('Y_m');

		return  $this->getDbWeb()->selectAll(
			'SELECT *, \''.$date.'\' as date FROM `post_videos_' . $date . '`
			WHERE
			`has_thumbnail` = ? LIMIT ?',
			array(
				static::HAS_THUMB_UNKNOWN,
				$limit
			)
		);
	}
	public function getVideoPosts(array $post)
	{
		$date     = date('Y_m', $post['pub_time']);
		$rows  = $this->getDbWeb()->selectAll(
			'SELECT * FROM `post_videos_' . $date . '`
			WHERE
			`post_id` = ? AND
			`author_id` = ?',
			array(
				$post['post_id'],
				$post['author_id']
			)
		);
		$videos = array();
		foreach($rows as $row)
		{
			if($row['type'] == static::VIDEO_TYPE_YOUTUBE)
			{
				$videos[$row['video_id']] = $row+array(
					'url' => '//www.youtube.com/embed/' . $row['video_id'],
					'html' => '<iframe width="560" height="315" src="//www.youtube.com/embed/'.$row['video_id'].'"
					frameborder="0" allowfullscreen></iframe>',
					'embed_id' => $row['embed_id']
				);
			}
		}
		return $videos;
	}

	public function savePostYoutubeVideos(array $post, array $videos)
	{
		$date     = date('Y_m', $post['pub_time']);
		$postId   = $post['post_id'];
		$authorId = $post['author_id'];
		foreach($videos as $video)
		{
			$this->getDbMaster()->query(
				'REPLACE INTO `post_videos_' . $date . '` SET
			`post_id` = ?,
			`author_id` = ?,
			`video_id` = ?,
			`video_url` = ?,
			`has_thumbnail` = ?,
			`embed_id` = ?,
			`type` = ?
			',
				array(
					$postId,
					$authorId,
					$video['id'],
					'',
					static::HAS_THUMB_UNKNOWN,
					$video['embed_id'],
					static::VIDEO_TYPE_YOUTUBE
				)
			);
		}
		return true;
	}
}
