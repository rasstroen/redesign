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
		return $this->application->db->web->selectAll('SELECT * FROM `theme`', array(), 'theme_id');
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
