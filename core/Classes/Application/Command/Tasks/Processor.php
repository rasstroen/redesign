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
				$this->runWorker($queueId, $queueInfo);
			}
		}
	}

	private function runWorker($queueId, array $queueInfo)
	{
		$movedTasksCount    = $this->application->bll->queue->moveTasksToWorker(
			$queueId,
			$queueInfo['tasks_per_worker']
		);
		if($movedTasksCount)
		{
			$this->log('created worker for queue ' . $queueInfo['name'] . ' with ' . $movedTasksCount . ' tasks');
		}
		else
		{
			$this->log('queue ' . $queueInfo['name'] . ' has no tasks');
		}
	}
}
