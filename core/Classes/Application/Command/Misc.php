<?php
namespace Application\Command;

use Application\BLL\Posts;
use Application\Module\Post;

class Misc extends Base
{
	public function actionDaily()
	{
		$date           = new \DateTime();
		$interval       = new \DateInterval('P1M');
		$currentMonth   = $date->format('Y_m');
		$nextMonth      = $date->add($interval)->format('Y_m');

		$this->log($currentMonth . '  ' . $nextMonth);
		/**
		 * checking date tables
		 */
		$monthTables = array('rubric_link_');
		foreach($monthTables as $tablePrefix)
		{
			$this->log('Creating table ' . $tablePrefix . $nextMonth);
			$this->application->db->master->query('CREATE TABLE IF NOT EXISTS `' . $tablePrefix . $nextMonth . '` LIKE `' . $tablePrefix . $currentMonth .'`');
		}

		$this->deleteOldThemePosts();
	}

	public function deleteOldThemePosts()
	{
		$oldTime = time() - Posts::POST_ACTIVE_LIFE_DAYS * 24 * 60 * 60;
		$this->application->db->master->query('DELETE FROM `theme_post_active` WHERE `pub_time` < ?', array($oldTime));
	}
}