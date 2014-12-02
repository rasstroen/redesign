<?php
namespace Application\BLL;
use Classes\Exception\InvalidArgument;

/**
 * Class Author
 * @package Application\BLL
 */
class Author extends BLL
{
	const AUTHOR_TYPE_USER      = 0;
	const AUTHOR_TYPE_COMMUNITY = 1;
	private $fields = array(
		'username'                  => 1,// ppb_username
		'userinfo_change_time'      => 1,// last change time
		'is_community'              => 1,// is it community profile
		'url'                       => 1,// livejournal url
		'userinfo_full_change_time' => 1,// last full info update

		'journal_posted'            => 1,
		'journal_commented'         => 1,
		'journal_comments_received' => 1,
		'journal_created'           => 1,
		'journal_title'             => 1,
		'journal_subtitle'          => 1,
		'journal_country_code'      => 1,
		'journal_city_name'         => 1,
		'journal_pic'               => 1,
		'journal_bio'               => 1,

		'rating_last_position'      => 1,
		'position'                  => 1,
		'rating'                    => 1,
	);

	public function getTop($type, $page, $perPage = 20)
	{
		return $this->getDbWeb()->selectAll(
			'SELECT * FROM `author` WHERE `is_community`=? AND `position` > 0 ORDER BY `position` LIMIT ?, ? ',
			array(
				$type,
				($page - 1) * $perPage,
				$perPage
			)
		);
	}

	public function getCurrentAuthorRatings($authorIds)
	{
		return $this->application->db->master->selectAll(
			'SELECT author_id, SUM(`rating`) as rating FROM `active_posts` WHERE `author_id` IN(?) GROUP BY `author_id`',
			array(
				$authorIds
			),
			'author_id'
		);
	}


	public function getTopQueryUse($type)
	{
		return $this->getDbWeb()->selectQueryUse(
			'SELECT `author_id`, `rating` FROM `author` WHERE `is_community`=?',
			array(
				$type,
			)
		);
	}

	public function getByIds(array $authorIds)
	{
		if(!count($authorIds))
		{
			return array();
		}
		return $this->getDbMaster()->selectAll(
			'SELECT * FROM `author` WHERE `author_id` IN(?)',
			array(
				$authorIds
			),
			'author_id'
		);
	}

	public function getByUserName($userName)
	{
		return $this->getDbMaster()->selectRow(
			'SELECT * FROM `author` WHERE `username` = ?',
			array($userName)
		);
	}

	public function getIdsByOldestInfoFullUpdate($limit, $oldTime = null)
	{
		if(null === $oldTime)
		{
			$oldTime = time() - 24*60*60 * 7;
		}
		return $this->getDbMaster()->selectColumn(
			'SELECT `author_id` FROM `author` WHERE `userinfo_change_time` > 0 AND `userinfo_full_change_time` < ? ORDER BY `userinfo_full_change_time` LIMIT ?',
			array(
				$oldTime,
				$limit
			)
		);
	}

	public function insert($userName, array $userData)
	{
		$sqlParts   = array();
		$values     = array();
		foreach($userData as $field => $value)
		{
			if(!isset($this->fields[$field]))
			{
				continue;
			}
			if($field !== 'username')
			{
				$sqlParts[] = '`' . $field . '` = ?';
				$values[]   = $value;
			}
		}

		$valuesCopy = $values;
		$values[]   = $userName;

		$this->getDbMaster()->query(
			'INSERT INTO `author` SET ' . implode(',', $sqlParts) . ', `username` = ? ON DUPLICATE KEY UPDATE '. implode(',', $sqlParts),
			array_merge($values, $valuesCopy)
		);
		return $this->getDbMaster()->lastInsertId();
	}

	public function updateById($authorId, $userData, $table ='author')
	{
		$sqlParts   = array();
		$values     = array();
		foreach($userData as $field => $value)
		{
			if(!isset($this->fields[$field]))
			{
				continue;
			}
			if($field !== 'username')
			{
				$sqlParts[] = '`' . $field . '` = ?';
				$values[]   = $value;
			}
		}

		$values[] = $authorId;

		$this->getDbMaster()->query(
			'UPDATE `' . $table . '` SET ' . implode(',', $sqlParts) . ' WHERE `author_id` = ?',
			$values
		);
	}

	public function updateInfoByUserName($userName, array $userData)
	{
		$sqlParts   = array();
		$values     = array();
		foreach($userData as $field => $value)
		{
			if(!isset($this->fields[$field]))
			{
				continue;
			}
			if($field !== 'username')
			{
				$sqlParts[] = '`' . $field . '` = ?';
				$values[]   = $value;
			}
		}

		$values[] = $userName;

		$this->getDbMaster()->query(
			'UPDATE `author` SET ' . implode(',', $sqlParts) . ' WHERE `username` = ?',
			$values
		);
	}
}
