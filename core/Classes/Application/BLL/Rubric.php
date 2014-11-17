<?php
namespace Application\BLL;
/**
 * Class Rubric
 * @package Application\BLL
 */
class Rubric extends BLL
{
	public function getAll()
	{
		return $this->getDbMaster()->selectAll('SELECT * FROM `rubric` ORDER BY `parent_id`, `position`', array(), 'rubric_id');
	}

	public function addToParent($parentId, array $data)
	{
		return $this->getDbMaster()->query(
			'INSERT INTO `rubric` SET
				`name`      = ?,
				`title`     = ?,
				`parent_id` = ?',
			array(
				$data['name'],
				$data['title'],
				$parentId
			)
		);
	}
}
