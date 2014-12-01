<?php
namespace Application\Command;

class Misc extends Base
{
	public function actionDaily()
	{
		$date           = new \DateTime();
		$interval       = new \DateInterval('P1M');
		$currentMonth   = $date->format('Y_m');
		$previousMonth  = $date->sub($interval)->format('Y_m');

		$this->log($currentMonth . '  ' . $previousMonth);
		/**
		 * checking date tables
		 */
		$monthTables = array('rubric_link_');
		foreach($monthTables as $tablePrefix)
		{
			$this->log('Creating table ' . $tablePrefix . $currentMonth);
			$this->application->db->master->query('CREATE TABLE IF NOT EXISTS `' . $tablePrefix . $currentMonth . '` LIKE `' . $tablePrefix . $previousMonth .'`');
		}

	}
}