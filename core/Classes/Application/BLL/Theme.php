<?php
namespace Application\BLL;

/**
 * Class Theme
 * @package Application\BLL
 */
class Theme extends BLL
{
	private $existsTables = null;

	public function getAll()
	{
		return $this->application->db->web->selectAll('SELECT * FROM `theme`', array(), 'theme_id');
	}

	public function getPostsIds($themeId)
	{
		return $this->application->db->web->selectAll(
			'SELECT * FROM `theme_post_active` WHERE `theme_id` = ? ORDER BY pub_time DESC',
			array($themeId)
		);
	}

	public function createThemePostsTable($tableName)
	{
		if(!$this->application->bll->posts->existsTable($tableName))
		{
			$query = 'CREATE TABLE IF NOT EXISTS `' . $tableName . '` (
		        `post_id` int(10) unsigned NOT NULL,
		        `author_id` int(10) unsigned NOT NULL,
		        `theme_id` int(10) unsigned NOT NULL,
		        `pub_time`  int(10) unsigned NOT NULL,
		        PRIMARY KEY (`post_id`,`author_id`),
		        KEY `theme_id` (`theme_id`,`pub_time`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';
			$this->application->db->master->query($query);
			$this->existsTables[$tableName] = true;
		}
	}

	public function bindPostToTheme($postId, $authorId, $themeId)
	{
		$post = $this->application->bll->posts->getPostByAuthorIdPostId(
			$postId, $authorId
		);

		$date = date('Y_m', $post['pub_time']);

		$this->createThemePostsTable('theme_post_' . $date);

		$this->getDbMaster()->query('INSERT INTO `theme_post_'.$date.'` SET
		`post_id`=?,
		`author_id`=?,
		`theme_id`=?,
		`pub_time`=?
		ON DUPLICATE KEY UPDATE post_id=?',
			array(
				$postId,
				$authorId,
				$themeId,
				$post['pub_time'],
				$postId
			)
		);

		$this->createThemePostsTable('theme_post_active');

		$this->getDbMaster()->query('INSERT INTO `theme_post_active` SET
		`post_id`=?,
		`author_id`=?,
		`theme_id`=?,
		`pub_time`=?
		ON DUPLICATE KEY UPDATE post_id=?',
		                            array(
			                            $postId,
			                            $authorId,
			                            $themeId,
			                            $post['pub_time'],
			                            $postId
		                            )
		);
	}

	public function prepareThemesPhrases(array &$themes)
	{
		$phrases = $this->getDbWeb()->selectAll(
			'SELECT * FROM `theme_phrases` WHERE `theme_id` IN (?)',
			array(array_keys($themes))
		);
		foreach($phrases as $phrase)
		{
			$themes[$phrase['theme_id']]['phrases'][] = $phrase['phrase'];
		}
	}

	public function getById($themeId)
	{
		return $this->application->db->web->selectRow(
			'SELECT * FROM `theme` WHERE `theme_id` = ?',
			array(
				$themeId
			)
		);
	}

	public function delPhrase($themeId, $phrase)
	{
		return $this->getDbMaster()->query(
			'DELETE FROM `theme_phrases` WHERE `theme_id` = ? AND phrase = ?',
			array(
				$themeId,
				$phrase
			)
		);
	}

	public function addPhrase($themeId, $phrase)
	{
		$this->getDbMaster()->query(
			'INSERT INTO `theme_phrases` SET `theme_id` = ?, phrase = ?',
			array(
				$themeId,
				$phrase
			)
		);

		return $this->getDbMaster()->lastInsertId();
	}

	public function add(
		$title,
		$name,
		$finish,
		$description
	)
	{
		$this->getDbMaster()->query(
			'INSERT INTO `theme` (`title`, `name`, `finish`, `description`) VALUES(?,?,?,?)',
			array(
				$title,
				$name,
				$finish,
				$description
			)
		);

		return $this->getDbMaster()->lastInsertId();
	}

	public function updateById(
		$themeId,
		$title,
		$name,
		$finish,
		$description
	)
	{
		$this->getDbMaster()->query(
			'UPDATE `theme` SET `title` =? , `name` = ?, `finish` =? , `description` =?
			WHERE theme_id=?',
			array(
				$title,
				$name,
				$finish,
				$description,
				$themeId
			)
		);
	}
}
