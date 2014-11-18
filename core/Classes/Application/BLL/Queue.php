<?php
namespace Application\BLL;
/**
 * Class Queue
 * @package Application\BLL
 */
class Queue extends BLL
{
	/**
	 * Апдейтим информацию по автору
	 */
	const QUEUE_AUTHOR_UPDATE_INFO      = 1;

	/**
	 * Вытаскиваем свежие посты автора
	 */
	const QUEUE_AUTHOR_FETCH_RSS        = 2;

	/**
	 * Вытаскиваем полную информацию по авторам
	 */
	const QUEUE_AUTHOR_FETCH_ALL_INFO   = 3;


	private $queues = array(
		self::QUEUE_AUTHOR_UPDATE_INFO => array(
			'name'              => 'QUEUE_AUTHOR_UPDATE_INFO',
			'priority'          => 1,
			'workers'           => 6,
			'tasks_per_worker'  => 50,
			'command'           => 'Author',
			'method'            => 'updateInfo',
			'enabled'           => 1,
		),
		self::QUEUE_AUTHOR_FETCH_RSS => array(
			'name'              => 'QUEUE_AUTHOR_FETCH_RSS',
			'priority'          => 1,
			'workers'           => 2,
			'tasks_per_worker'  => 10,
			'command'           => 'Author',
			'method'            => 'fetchRss',
			'enabled'           => 0,
		),
		self::QUEUE_AUTHOR_FETCH_ALL_INFO => array(
			'name'              => 'QUEUE_AUTHOR_FETCH_ALL_INFO',
			'priority'          => 1,
			'workers'           => 1,
			'tasks_per_worker'  => 1,
			'command'           => 'Author',
			'method'            => 'fetchFullInfo',
			'enabled'           => 1,
		),
);
	public function getAll()
	{
		return $this->queues;
	}

	public function getQueue($queueId)
	{
		return isset($this->queues[$queueId]) ? $this->queues[$queueId] : null;
	}

	public function getRandomWorkerIdByQueueId($queueId)
	{
		return $this->application->db->master->selectSingle(
			'SELECT `worker_id` FROM `queue_workers` WHERE `queue_id` = ? ORDER BY `pid` LIMIT 1',
			array($queueId)
		);
	}

	public function getWorkerById($workerId)
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

			return array($this->getDbMaster()->lastInsertId(), count($tasks));
		}
		return array(0 , 0);
	}

	public function getNotRunnedWorkersIds()
	{
		return $this->application->db->master->selectColumn(
			'SELECT `worker_id` FROM `queue_workers` WHERE `pid` = 0'
		);
	}

	public function getQueueWorkers($queueId)
	{
		$workers = $this->application->db->master->selectAll(
			'SELECT * FROM `queue_workers` WHERE `queue_id` = ?',
			array(
				$queueId
			)
		);

		return $workers;
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
	public function addTask($queueId, $taskId = 'unique', array $data = array(), $waitTime = 0)
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
