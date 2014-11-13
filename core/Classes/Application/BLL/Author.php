<?php
namespace Application\BLL;
/**
 * Class Author
 * @package Application\BLL
 */
class Author extends BLL
{
	public function getByUserName($userName)
	{
		return $this->getDbMaster()->selectRow(
			'SELECT * FROM `author` WHERE `username` = ?',
			array($userName)
		);
	}

	public function insert($userName, array $userData)
	{
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
