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

}
