<?php
namespace Application\Command\Tasks;

use Application\Command\Base;

class Processor extends Base
{
	/**
	 * Выбираем текущие задачи из очередей,
	 * удаляем задачи и запускаем обработчики
	 */
	public function actionRun()
	{
		$queues = $this->application->bll->queue->getAll();

		foreach($queues as $queueId => $queue)
		{
			$queueInfo  = $this->application->bll->queue->getQueueStatus($queueId);
			$this->log('queue ' . $queueInfo['name'] . ' has ' . $queueInfo['current_workers_count'] . '/' . $queueInfo['workers'] . ' workers');
			if($queueInfo['free_workers'] > 0)
			{
				/**
				 * Есть свободные обработчики данного типа тасков
				 */
				for($i =0; $i < $queueInfo['free_workers']; $i++)
				{
					$this->createWorker($queueId, $queueInfo);
				}
			}
		}
	}

	public function workerProcess($workerId)
	{
		$worker     = $this->application->bll->queue->getById($workerId);
		$workerInfo = $this->application->bll->queue->getQueue($worker['queue_id']);

		$workerClassName    = '\Application\Command\Tasks\Worker\\' . $workerInfo['command'];
		$workerMethod       = 'method' . ucfirst($workerInfo['method']);

		$workerObject       = new $workerClassName($this->application);
		$workerObject->$workerMethod($worker);

		$this->log('deleted worker ' . $workerId);
		$this->application->bll->queue->deleteWorker($workerId);
	}

	/**
	 * Запускаем всех незапущенных воркеров
	 */
	public function actionRunWorkers()
	{
		$workersNotRunned   = $this->application->bll->queue->getNotRunnedWorkersIds();

		foreach($workersNotRunned as $workerId)
		{
			$pid = pcntl_fork();
			if($pid == -1)
			{
				continue;
			}
			if($pid > 0)
			{
				/**
				 * created child with pid=$pid, workerId=$workerId
				 */
				$this->application->db->reconnectAll();
			}
			else
			{
				/**
				 * it is a child process with workerId=$workerId
				 */
				$this->application->db->reconnectAll();
				$this->application->bll->queue->updatePid($workerId, getmypid());
				$this->log('Runned child: workerId=' . $workerId);
				$this->workerProcess($workerId);
				return;
			}
		}
	}

	public function actionRunWorker()
	{

	}

	private function createWorker($queueId, array $queueInfo)
	{
		$workerId    = $this->application->bll->queue->moveTasksToWorker(
			$queueId,
			$queueInfo['tasks_per_worker']
		);
		if($workerId)
		{
			$this->log('created worker for queue ' . $queueInfo['name'] . ', worker id:' . $workerId);
		}
		else
		{
			$this->log('queue ' . $queueInfo['name'] . ' has no tasks');
		}
	}
}