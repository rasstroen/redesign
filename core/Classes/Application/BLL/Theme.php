<?php
namespace Application\BLL;

/**
 * Class Theme
 * @package Application\BLL
 */
class Theme extends BLL
{
	public function getAll()
	{
		return $this->application->db->web->selectAll('SELECT * FROM `theme`');
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
