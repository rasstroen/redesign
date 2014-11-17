<?php
namespace Application\BLL;
use Classes\Exception\InvalidArgument;

/**
 * Class Author
 * @package Application\BLL
 */
class Author extends BLL
{
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
	);

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
			$oldTime = time() - 24*60*60;
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
