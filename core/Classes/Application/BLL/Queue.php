<?php
namespace Application\BLL;
/**
 * Class Queue
 * @package Application\BLL
 */
class Queue extends BLL
{
	const QUEUE_AUTHOR_UPDATE_INFO  = 1;
	const QUEUE_AUTHOR_FETCH_RSS    = 2;

	private $queues = array(
		self::QUEUE_AUTHOR_UPDATE_INFO => array(
			'name'              => 'QUEUE_AUTHOR_UPDATE_INFO',
			'priority'          => 1,
			'workers'           => 2,
			'tasks_per_worker'  => 10
		),
		self::QUEUE_AUTHOR_FETCH_RSS => array(
			'name'              => 'QUEUE_AUTHOR_FETCH_RSS',
			'priority'          => 1,
			'workers'           => 2,
			'tasks_per_worker'  => 10
		),
);
	public function getAll()
	{
		return $this->queues;
	}

	public function moveTasksToWorker($queueId, $maxTasksCount)
	{
		$tasks = $this->getDbMaster()->selectAll(
			'SELECT * FROM `queue_tasks_' . $queueId . '` WHERE `run_time` < ? ORDER BY `run_time` LIMIT ?',
			array(
				time(),
				$maxTasksCount
			),
			'task_id'
		);

		if(count($tasks))
		{
			$this->getDbMaster()->query(
				'DELETE FROM `queue_tasks_' . $queueId . '` WHERE `task_id`!=? AND `task_id` IN(?) AND `task_id` IN(?)',
				array(
					123,
					array_keys($tasks),
					array_keys($tasks)
				)
			);

			$this->getDbMaster()->query(
				'INSERT INTO `queue_workers`(`queue_id`, `tasks`, `create_time`) VALUES(?, ?, NOW())',
				array(
					$queueId,
					serialize($tasks)
				)
			);

			return count($tasks);
		}
		return 0;
	}

	public function getQueueStatus($queueId)
	{
		$queueInfo                          = $this->queues[$queueId];
		$queueInfo['current_workers_count'] = $this->application->db->master->selectSingle(
			'SELECT COUNT(1) FROM `queue_workers` WHERE `queue_id` = ?',
			array(
				$queueId
			)
		);

		$queueInfo['free_workers'] = $queueInfo['workers'] - $queueInfo['current_workers_count'];
		return $queueInfo;
	}

	/**
	 * @param $queueId
	 * @param $taskId
	 * @param array $data
	 * @param int $waitTime
	 * @throws \Exception
	 */
	public function addTask($queueId, $taskId, array $data, $waitTime = 0)
	{
		if(!is_numeric($taskId))
		{
			$taskId = intval(substr(md5($taskId), 0, 14), 16);
		}

		$serializedData = serialize($data);
		$runTime        = $waitTime + time();
		$this->getDbMaster()->query(
			'INSERT INTO `queue_tasks_' . $queueId . '` SET
				`task_id`   = ?,
				`data`      = ?,
				`run_time`  = ?
			ON DUPLICATE KEY UPDATE
				`data`      = ?,
				`run_time`  = ?',
			array(
				$taskId,
				$serializedData,
				$runTime,
				$serializedData,
				$runTime,
			)
		);
	}
}
