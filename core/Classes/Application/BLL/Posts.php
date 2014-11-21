<?php
namespace Application\BLL;
/**
 * Class Posts
 * @package Application\BLL
 */
class Posts extends BLL
{

	const PIC_STATUS_UNKNOWN        = 0;
	const PIC_STATUS_HAS_PIC        = 1;
	const PIC_STATUS_HASNOT_PIC     = 2;

	const VIDEO_STATUS_UNKNOWN      = 0;
	const VIDEO_STATUS_HAS_PIC      = 1;
	const VIDEO_STATUS_HASNOT_PIC   = 2;

	public function saveAuthorPost($authorId, $postData)
	{
		$pubTime                = strtotime($postData['pubdate']);
		$postData['pub_time']   = $pubTime;
		$postId                 = intval(array_pop(explode('/',$postData['url'])));


		$this->savePostToArchive($authorId, $postId, $postData);
		$this->savePostAuthorLink();
		$this->savePostTags();
	}

	private function savePostToArchive($authorId, $postId, $postData)
	{
		$tableName = '_posts_archive__' . date('Y_m', $postData['pub_time']);
		$this->createArchiveTable($tableName);

		$this->application->db->master->query('INSERT INTO `' . $tableName . '`
		SET
			`post_id`           = ?,
			`author_id`         = ?,
			`pub_time`          = ?,
			`update_time`       = ?,
			`title`             = ?,
			`comments`          = ?,
			`has_pic`           = ?,
			`has_video`         = ?,
			`short`             = ?,
			`text`              = ?
			ON DUPLICATE KEY UPDATE
			`pub_time`          = ?,
			`update_time`       = ?,
			`title`             = ?,
			`comments`          = ?,
			`short`             = ?,
			`text`              = ?',
			array(
				$postId,
				$authorId,
				$postData['pub_time'],
				time(),
				$postData['title'],
				$postData['comments'],
				self::PIC_STATUS_UNKNOWN,
				self::VIDEO_STATUS_UNKNOWN,
				$this->shortText($postData['description']),
				$postData['description'],

				$postData['pub_time'],
				time(),
				$postData['title'],
				$postData['comments'],
				$this->shortText($postData['description']),
				$postData['description'],
			)
		);
	}

	public function shortText($text, $words = 200) {
		$text       = str_replace(
			array('<br>', '<br />', '<br/>'), ' ' , $text
		);
		$noHtml    = trim(strip_tags($text));
		$exploded   = explode(' ', $noHtml, ($words + 1));
		if (count($exploded) > $words)
		{
			array_pop($exploded);
			$exploded[] = '...';
		}
		return implode(' ', $exploded);
	}

	public function createArchiveTable($tableName)
	{
		$query = 'CREATE TABLE IF NOT EXISTS `' . $tableName . '` (
        `post_id` int(10) unsigned NOT NULL,
        `author_id` int(10) unsigned NOT NULL,
        `pub_time` int(10) unsigned NOT NULL,
        `update_time` int(10) unsigned NOT NULL,
        `title` varchar(255) NOT NULL,
        `comments` int(10) unsigned NOT NULL,
        `has_pic` tinyint(3) unsigned NOT NULL,
        `has_video` tinyint(3) unsigned NOT NULL,
        `short` text NOT NULL,
        `text` longtext NOT NULL,
        PRIMARY KEY (`post_id`,`author_id`),
        KEY `has_pic` (`has_pic`),
        KEY `has_video` (`has_video`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		return $this->application->db->master->query($query);
	}


	/**
	 * Обрабатываем данные из рейтинга яндекса - ставим задачи на обновление постов
	 * автора, задачу на добавление/обновление автора.
	 *
	 * @param array $item
	 */
	public function processYandexApiData(array $item)
	{
		preg_match('/http:\/\/(.*).livejournal.com\//isU', $item['author'], $matches);
		if(count($matches))
		{
			/**
			 * Это пользователь ЖЖ
			 * Добавляем задачу на обновление информации о пользователе
			 */
			$this->application->bll->queue->addTask(
				Queue::QUEUE_AUTHOR_UPDATE_INFO,
				$item['yablogs:ppb_username'],
				array(
					'username'      => $item['yablogs:ppb_username'],
					'url'           => $item['author'],
				)
			);
			/**
			 * Добавляем задачу на обновление ленты пользователя - если есть в выдаче яндекса,
			 * значит есть пост в топе, нужно его обновить прямо сейчас
			 */
			$this->application->bll->queue->addTask(
				Queue::QUEUE_AUTHOR_FETCH_RSS,
				$item['yablogs:ppb_username'],
				array(
					'username'      => $item['yablogs:ppb_username'],
					'url'           => $item['author'],
					'lastPostTime'  => strtotime($item['pubDate']),
				)
			);
			return true;
		}
		return false;
	}
}
