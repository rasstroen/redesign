<?php
namespace Application\BLL;
/**
 * Class Rubric
 * @package Application\BLL
 */
class Rubric extends BLL
{

	public function linkPostToRubricWithWord($phraseId, $postId, $authorId, $pubDate, $rubricId)
	{
		$this->getDbMaster()->query(
			'INSERT INTO rubric_auto_link(rubric_id, post_id, author_id, pub_date, phrase_id) VALUES(?,?,?,?,?)',
			array(
				$rubricId, $postId, $authorId, $pubDate, $phraseId
			)
		);
	}

	public function getAll()
	{
		return $this->getDbMaster()->selectAll('SELECT * FROM `rubric` ORDER BY `parent_id`, `position`', array(), 'rubric_id');
	}

	public function getById($rubricId)
	{
		return $this->getDbMaster()->selectRow('SELECT * FROM `rubric` WHERE `rubric_id` = ?', array($rubricId));
	}

	public function restore($rubricId)
	{
		return $this->getDbMaster()->query(
			'UPDATE `rubric` SET `deleted` = 0 WHERE `rubric_id` =? OR `parent_id` = ?',
			array(
				$rubricId,
				$rubricId
			)
		);
	}

	public function setHiddenWithChilds($rubricId)
	{
		return $this->getDbMaster()->query(
			'UPDATE `rubric` SET `deleted` = 1 WHERE `rubric_id` =? OR `parent_id` = ?',
			array(
				$rubricId,
				$rubricId
			)
		);
	}

	public function deleteWithChilds($rubricId)
	{
		return $this->getDbMaster()->query(
			'DELETE FROM `rubric` WHERE `rubric_id` =? OR `parent_id` = ?',
			array(
				$rubricId,
				$rubricId
			)
		);
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

	public function edit($rubricId, array $data)
	{
		return $this->getDbMaster()->query(
			'UPDATE `rubric` SET
				`name`      = ?,
				`title`     = ?
				WHERE
				`rubric_id` = ?',
			array(
				$data['name'],
				$data['title'],
				$rubricId
			)
		);
	}
}
