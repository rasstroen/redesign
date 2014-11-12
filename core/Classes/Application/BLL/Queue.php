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
			'workers'           => 4,
			'tasks_per_worker'  => 10,
			'command'           => 'Author',
			'method'            => 'updateInfo'
		),
		self::QUEUE_AUTHOR_FETCH_RSS => array(
			'name'              => 'QUEUE_AUTHOR_FETCH_RSS',
			'priority'          => 1,
			'workers'           => 2,
			'tasks_per_worker'  => 10,
			'command'           => 'Author',
			'method'            => 'fetchRss'
		),
);
	public function getAll()
	{
		return $this->queues;
	}

	public function getQueue($queueId)
	{
		return $this->queues[$queueId];
	}

	public function getById($workerId)
	{
		return $this->application->db->master->selectRow(
			'SELECT * FROM `queue_workers` WHERE `worker_id` = ?',
			array($workerId)
		);
	}

	public function deleteWorker($workerId)
	{
		return $this->application->db->master->selectRow(
			'DELETE FROM `queue_workers` WHERE `worker_id` = ?',
			array($workerId)
		);
	}


	public function updatePid($workerId, $pid)
	{
		return $this->application->db->master->query(
			'UPDATE `queue_workers` SET `pid` = ? WHERE `worker_id` = ?',
			array(
				$pid,
				$workerId
			)
		);
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
				'DELETE FROM `queue_tasks_' . $queueId . '` WHERE `task_id` IN(?)',
				array(
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

			return $this->getDbMaster()->lastInsertId();
		}
		return 0;
	}

	public function getNotRunnedWorkersIds()
	{
		return $this->application->db->master->selectColumn(
			'SELECT `worker_id` FROM `queue_workers` WHERE 1 OR `pid` = 0'
		);
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