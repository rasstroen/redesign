<?php
namespace Application\BLL;

/**
 * Class Rubric
 * @package Application\BLL
 */
class Publics extends BLL
{
	public function getAllPublics()
	{
		return $this->getDbWeb()->selectAll('SELECT * FROM `public`', array(), 'id');
	}

	public function setLastPostDate($publicId, $date, $offset)
	{
		return $this->getDbMaster()->query(
			'UPDATE `public` SET `last_post` =?, `offset` = ? WHERE `id` = ?',
			array($date, $offset, $publicId)
		);
	}

	public function savePosts($public, array $posts = null, $aid = 'wall')
	{
		$aid          = (int) $aid;
		$publicId     = $public['id'];
		$lastPostTime = $public['last_post'];

		if(count($posts))
		{
			foreach($posts as $post)
			{
				$lastPostTime = max($lastPostTime, $post['created']);

				$post['src_xxbig'] = isset($post['src_xxbig']) ? $post['src_xxbig'] : '';
				$post['src_xbig']  = isset($post['src_xbig']) ? $post['src_xbig'] : '';
				$post['width']     = isset($post['width']) ? $post['width'] : '';
				$post['height']    = isset($post['height']) ? $post['height'] : '';


				$this->getDbMaster()->query(
					'INSERT INTO `feed` SET
                `gid`=?,
                `aid`=?,
                `pid`=?,
                `user_id`=?,
                `text`=?,
                `src`=?,
                `src_big`=?,
                `src_xbig`=?,
                `src_xxbig`=?,
                `src_small`=?,
                `width`=?,
                `heigth`=?,
                `likes`=?,
                `created`=?
                    ON DUPLICATE KEY UPDATE
                `gid`=?,
                `aid`=?,
                `pid`=?,
                `user_id`=?,
                `text`=?,
                `src`=?,
                `src_big`=?,
                `src_xbig`=?,
                `src_xxbig`=?,
                `src_small`=?,
                `width`=?,
                `heigth`=?,
                `likes`=?,
                `created`=?',
					array(
						$publicId,
						$aid,
						$post['pid'],
						$post['user_id'],
						$post['text'],
						$post['src'],
						$post['src_big'],
						$post['src_xbig'],
						$post['src_xxbig'],
						$post['src_small'],
						$post['width'],
						$post['heigth'],
						$post['likes']['count'],
						$post['created'],
						//
						$publicId,
						$aid,
						$post['pid'],
						$post['user_id'],
						$post['text'],
						$post['src'],
						$post['src_big'],
						$post['src_xbig'],
						$post['src_xxbig'],
						$post['src_small'],
						$post['width'],
						$post['heigth'],
						$post['likes']['count'],
						$post['created'],
					)
				);
			}
		}
	}

	public function getById($publicId)
	{
		return $this->getDbWeb()->selectRow(
			'SELECT * FROM `public` WHERE `id` = ?',
			array(
				$publicId
			),
			'id'
		);
	}
}
