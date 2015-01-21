<?php
namespace Application\Command;

use Application\BLL\Posts;
use Application\BLL\Queue;
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
		$monthTables = array('rubric_link_', 'rubric_link_abandon_', 'post_videos_');
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

	/**
	 * Наполняем очередь для авторов, по которым пора вытащить подробную информацию.
	 * Мы постоянно получаем короткую информацию об авторах у Яндекса.
	 * Дополнительно нужно вытащить полную информацию - аватар, тайтл журнала и т.п.
	 */
	public function actionFillVideoUpdateQueue()
	{
		/**
		 * Если запущен воркер обновления авторов - засыпаем обратно
		 */
		$worker = $this->application->bll->queue->getRandomWorkerIdByQueueId(Queue::QUEUE_POSTS_PROCESS_POSTS_VIDEOS_THUMBS);
		if(count($worker))
		{
			$this->log('waiting for existing worker');
			return;
		}

		$videoIds = $this->application->bll->video->getWithUnknownThumbs(100);
		$this->log(count($videoIds) . ' videos added for full update');
		/**
		 * Только 1 таск
		 */
		$taskId = 'unique';
		$this->application->bll->queue->addTask(
			Queue::QUEUE_POSTS_PROCESS_POSTS_VIDEOS_THUMBS,
			$taskId,
			$videoIds,
			0,
			true
		);
	}
}