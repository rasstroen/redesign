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
		'username'              => 1,// ppb_username
		'userinfo_change_time'  => 1,// last change time
		'is_community'          => 1,// is it community profile
		'url'                   => 1,// livejournal url
	);

	public function getByUserName($userName)
	{
		return $this->getDbMaster()->selectRow(
			'SELECT * FROM `author` WHERE `username` = ?',
			array($userName)
		);
	}

	public function insert($userName, array $userData)
	{
		foreach($userData as $field => $value)
		{
			if(!isset($this->fields[$field]))
			{
				throw new InvalidArgument('illegal field' . $field);
			}
		}

		$sqlParts   = array();
		$values     = array();
		foreach($userData as $field => $value)
		{
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
		foreach($userData as $field => $value)
		{
			if(!isset($this->fields[$field]))
			{
				throw new InvalidArgument('illegal field' . $field);
			}
		}

		$sqlParts   = array();
		$values     = array();
		foreach($userData as $field => $value)
		{
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
