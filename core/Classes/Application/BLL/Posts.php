<?php
namespace Application\BLL;
/**
 * Class Posts
 * @package Application\BLL
 */
class Posts extends BLL
{
	private $existsTables = null;

	const SHARDS_COUNT_AUTHOR       = 128;

	const POST_ACTIVE_LIFE_DAYS     = 10;

	const PIC_STATUS_UNKNOWN        = 0;
	const PIC_STATUS_HAS_PIC        = 1;
	const PIC_STATUS_HASNOT_PIC     = 2;

	const VIDEO_STATUS_UNKNOWN      = 0;
	const VIDEO_STATUS_HAS_PIC      = 1;
	const VIDEO_STATUS_HASNOT_PIC   = 2;

	public function preparePosts(array &$posts)
	{
		foreach ($posts as $post)
		{
			$authorIds[] = 	$post['author_id'];
		}

		$authors = $this->application->bll->author->getByIds($authorIds);

		foreach ($posts as &$post)
		{
			$post['author'] = $authors[$post['author_id']];
			$post['short'] 	= $this->shortText($post['short'], 70);
			$post['pub_date']	= date('d.m.y H:i', $post['pub_time']);
			$post['title']	= $this->shortText($post['title'], 70);
		}
		unset($post);
	}

	public function getPopularPosts($limit = 3)
	{
		$postsIds = $this->application->db->master->selectAll(
			'SELECT * FROM `active_posts` ORDER BY `rating` DESC LIMIT ?',
			array($limit)
		);

		return $this->getPostsByIds($postsIds);
	}

	public function getNewPosts($limit = 3)
	{
		$postsIds = $this->application->db->master->selectAll(
			'SELECT * FROM `active_posts` ORDER BY `pub_time` DESC LIMIT ?',
			array($limit)
		);

		return $this->getPostsByIds($postsIds);
	}

	private function getPostsByIds(array $ids = array())
	{
		$i=0;
		foreach($ids as $data)
		{
			$month = date('Y_m', $data['pub_time']);
			$i++;
			$postsByMonthsPairs[$month][] = '(?, ?)';
			$postsByMonthsPostIds[$month][] = '?';
			$values[$month]['post_ids'][$data['post_id']] 	= $data['post_id'];
			$values[$month]['ids'][] 						= $data['post_id'];
			$values[$month]['ids'][] 						= $data['author_id'];
		}
		$allPosts = array();
		foreach ($postsByMonthsPostIds as $month => $data)
		{

			$postsMonths = $this->application->db->master->selectAll('SELECT * FROM `_posts_archive__'.$month.'` WHERE
			`post_id` IN ('. implode(',', $postsByMonthsPostIds[$month]).') AND (`post_id`, `author_id`) IN (' . implode(',', $postsByMonthsPairs[$month]) .')',
					$values[$month]['post_ids'] + $values[$month]['ids']
				);
			foreach($postsMonths as $post)
			{
				$allPosts[$post['author_id'].'_'.$post['post_id']] = $post;
			}
		}

		foreach($ids as $data)
		{
			$out[] = $allPosts[$data['author_id'].'_'.$data['post_id']];
		}

		return $out;
	}

	public function getByPeriodFromDateTable($start, $end, $table)
	{
		return $this->application->db->master->selectAll('SELECT `post_id`, `author_id`, `pub_time`, `has_pic`, `has_video`, `comments` FROM `_posts_archive__' . $table. '`
			WHERE
			`pub_time` > ? AND
			`pub_time` < ?
		', array(
				$start,
				$end
			));
	}

	public function saveAuthorPost($authorId, $postData)
	{
		$pubTime                = strtotime($postData['pubdate']);
		$postData['pub_time']   = $pubTime;
		$postId                 = intval(array_pop(explode('/',$postData['url'])));
		$this->savePostToArchive($authorId, $postId, $postData);
		$this->savePostAuthorLink($authorId, $postId, $pubTime);
		$this->savePostTags($authorId, $postId, $postData);
		return $postId;
	}

	private function savePostTags($authorId, $postId, $postData)
	{


		$tags  = isset($postData['tags']) ? $postData['tags'] : array();
		if(count($tags))
		{
			$tableName = '_posts_author_tags___' . ($authorId % self::SHARDS_COUNT_AUTHOR);
			$this->createAuthorTagsTable($tableName);
		}

		foreach($tags as $tag)
		{
			$this->application->db->master->query('INSERT INTO `' . $tableName . '`
			SET
				`post_id`               = ?,
				`author_id`             = ?,
				`tag`                   = ?
				ON DUPLICATE KEY UPDATE
				`tag`            = ?',
				array(
					$postId,
					$authorId,
					$tag,
					$tag
				)
			);
		}
	}

	private function savePostAuthorLink($authorId, $postId, $pubTime)
	{
		$tableName = '_posts_author__' . ($authorId % self::SHARDS_COUNT_AUTHOR);
		$this->createAuthorPostLinkTable($tableName);

		$yearMonth = date('Y_m', $pubTime);

		$this->application->db->master->query('INSERT INTO `' . $tableName . '`
		SET
			`post_id`               = ?,
			`author_id`             = ?,
			`year_month`            = ?
			ON DUPLICATE KEY UPDATE
			`year_month`            = ?',
			array(
				$postId,
				$authorId,
				$yearMonth,
				$yearMonth
			)
		);

	}

	public function savePostToActive($postId, $authorId , $postData)
	{
		$this->createActivePostsTable('active_posts');
		$this->application->db->master->query('INSERT INTO `active_posts`
			SET
				`post_id`           = ?,
				`author_id`         = ?,
				`pub_time`          = ?,
				`comments`          = ?,
				`has_pic`           = ?,
				`has_video`         = ?,
				`rating`            = ?
				ON DUPLICATE KEY UPDATE
				`pub_time`          = ?,
				`comments`          = ?,
				`has_pic`           = ?,
				`has_video`         = ?,
				`rating`            = ?
			', array(
			$postId,
			$authorId,
			$postData['pub_time'],
			$postData['comments'],
			$postData['has_pic'],
			$postData['has_video'],
			$postData['rating'],

			$postData['pub_time'],
			$postData['comments'],
			$postData['has_pic'],
			$postData['has_video'],
			$postData['rating'],
		));
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

	public function shortText($text, $words = 200)
	{
		global $t;

		$text = html_entity_decode(str_replace('&nbsp;',' ',$text), ENT_QUOTES, 'UTF-8');

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
		mb_regex_encoding('UTF-8');
		return mb_ereg_replace('/ {2,}/','s',implode(' ', $exploded));
	}

	public function createAuthorTagsTable($tableName)
	{
		if(!$this->existsTable($tableName))
		{
			$query = 'CREATE TABLE IF NOT EXISTS `' . $tableName . '` (
		        `post_id` int(10) unsigned NOT NULL,
		        `author_id` int(10) unsigned NOT NULL,
		        `tag` varchar(128) NOT NULL,
		        PRIMARY KEY (`post_id`,`author_id`),
		        KEY `tag` (`author_id`, `tag`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';
			$this->application->db->master->query($query);
			$this->existsTables[$tableName] = true;
		}
	}

	public function createAuthorPostLinkTable($tableName)
	{
		if(!$this->existsTable($tableName))
		{
			$query = 'CREATE TABLE IF NOT EXISTS `' . $tableName . '` (
		        `post_id` int(10) unsigned NOT NULL,
		        `author_id` int(10) unsigned NOT NULL,
		        `year_month` varchar(7) NOT NULL,
		        PRIMARY KEY (`post_id`,`author_id`),
		        KEY `year_month` (`author_id`, `year_month`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';
			$this->application->db->master->query($query);
			$this->existsTables[$tableName] = true;
		}
	}

	public function createActivePostsTable($tableName)
	{
		if(!$this->existsTable($tableName))
		{
			$query = 'CREATE TABLE IF NOT EXISTS `' . $tableName . '` (
		        `post_id` int(10) unsigned NOT NULL,
		        `author_id` int(10) unsigned NOT NULL,
		        `pub_time` int(10) unsigned NOT NULL,
		        `comments` int(10) unsigned NOT NULL,
		        `has_pic` tinyint(3) unsigned NOT NULL,
		        `has_video` tinyint(3) unsigned NOT NULL,
		        `rating` int unsigned not null default 0,
		        PRIMARY KEY (`post_id`,`author_id`),
		        KEY `has_pic` (`has_pic`),
		        KEY `has_video` (`has_video`),
		        KEY `pub_time` (`pub_time`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';
			$this->application->db->master->query($query);
			$this->existsTables[$tableName] = true;
		}
	}

	public function createArchiveTable($tableName)
	{
		if(!$this->existsTable($tableName))
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
			$this->application->db->master->query($query);
			$this->existsTables[$tableName] = true;
		}
	}

	public function existsTable($tableName)
	{
		if(null == $this->existsTables)
		{
			$this->existsTables = array_flip($this->application->db->master->selectColumn('SHOW TABLES'));
		}
		return isset($this->existsTables[$tableName]);
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
				),
				60 // через минуту
			);
			return true;
		}
		return false;
	}
}
