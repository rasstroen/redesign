<?php
namespace Application\Command\Tasks;

use Application\Command\Base;

class Processor extends Base
{
	private $lockRunFileName            = '/tmp/lockProcessorRun';
	private $lockRunWorkersFileName     = '/tmp/lockWorkersProcessorRun';
	private $iterationsRun              = 100;
	private $iterationsRunWorkers       = 100;
	/**
	 * Выбираем текущие задачи из очередей,
	 * удаляем задачи и запускаем обработчики
	 */
	public function actionRun()
	{
		$this->checkLocks();
		if(file_get_contents($this->lockRunFileName))
		{
			$this->log('Processor already running');
			return;
		}
		file_put_contents($this->lockRunFileName, rand(1,100));
		$iterations = 0;
		while($iterations < $this->iterationsRun) {
			$iterations++;
			$this->log('Run iteration ' . $iterations);
			$queues = $this->application->bll->queue->getAll();

			foreach ($queues as $queueId => $queue) {
				$queueInfo = $this->application->bll->queue->getQueueStatus($queueId);
				if (!$queueInfo['enabled']) {
					$this->log('queue ' . $queueInfo['name'] . ' disabled');
					continue;
				}
				$this->log('queue ' . $queueInfo['name'] . ' has ' . $queueInfo['current_workers_count'] . '/' . $queueInfo['workers'] . ' workers');
				if ($queueInfo['free_workers'] > 0) {
					/**
					 * Есть свободные обработчики данного типа тасков
					 */
					for ($i = 0; $i < $queueInfo['free_workers']; $i++) {
						if (!$workerId = $this->createWorker($queueId, $queueInfo)) {
							break;
						}
					}
				}
			}
			sleep(10);
		}
		file_put_contents($this->lockRunFileName, 0);
	}

	public function checkLocks()
	{
		$lockTime           = fileatime($this->lockRunFileName);
		$lockTimeWorkers    = fileatime($this->lockRunWorkersFileName);
		$this->log(date('Y-m-d H:i:s', $lockTime). ' ' . $this->lockRunFileName);
		$this->log(date('Y-m-d H:i:s', $lockTimeWorkers) . ' ' . $this->lockRunWorkersFileName);

		if(time() - $lockTime > $this->iterationsRun * 10)
		{
			$this->log($this->lockRunFileName. ' unlinked');
			@unlink($this->lockRunFileName);
		}

		if(time() - $lockTimeWorkers > $this->iterationsRunWorkers * 10)
		{
			$this->log($this->lockRunWorkersFileName. ' unlinked');
			@unlink($this->lockRunWorkersFileName);
		}

		$pids = $this->application->bll->queue->getWorkersPids();
		foreach($pids as $workerId => $pid)
		{
			$command = "ps -ef | grep -v grep | awk '{print $2}'";
			$this->log($command);
			exec($command, $result);
			if(!array_search($pid, $result))
			{
				/**
				 * нет пида
				 */
				$this->log('worker with pid ' . $pid . 'not exists');
				$this->application->bll->queue->deleteWorker($workerId);
			}
		}

	}

	public function workerProcess($workerId)
	{
		$worker     = $this->application->bll->queue->getWorkerById($workerId);
		$workerInfo = $this->application->bll->queue->getQueue($worker['queue_id']);

		$workerClassName    = '\Application\Command\Tasks\Worker\\' . $workerInfo['command'];

		/**
		 * @var \Application\Command\Tasks\Worker\Base $workerObject
		 */
		$workerObject       = new $workerClassName($this->application, $worker);
		$this->log("$workerClassName::{$workerInfo['method']}();");
		$workerObject->runCommand($workerInfo['method']);
		$this->log('deleted worker ' . $workerId);
		$this->application->bll->queue->deleteWorker($workerId);
	}

	/**
	 * Запускаем всех незапущенных воркеров
	 */
	public function actionRunWorkers()
	{
		if(file_get_contents($this->lockRunWorkersFileName))
		{
			$this->log('RunWorkers already running');
			return;
		}
		file_put_contents($this->lockRunWorkersFileName, rand(1,100));
		$iterations = 0;

		while($iterations < $this->iterationsRunWorkers) {
			$iterations++;
			$this->log('run-workers iteration ' . $iterations);


			$workersNotRunned = $this->application->bll->queue->getNotRunnedWorkersIds();

			foreach ($workersNotRunned as $workerId) {
				$pid = pcntl_fork();
				if ($pid == -1) {
					continue;
				}
				if ($pid > 0) {
					/**
					 * created child with pid=$pid, workerId=$workerId
					 */
					$this->application->db->reconnectAll();
				} else {
					/**
					 * it is a child process with workerId=$workerId
					 */
					$this->application->db->reconnectAll();
					$this->application->bll->queue->updatePid($workerId, getmypid());
					$this->log('Runned child: workerId=' . $workerId);
					try {
						$this->workerProcess($workerId);
					} catch (\Exception $e) {
						$this->application->bll->queue->deleteWorker($workerId);
						throw $e;
					}
					return;
				}
			}
			sleep(10);
		}
		file_put_contents($this->lockRunWorkersFileName, 0);
	}

	/**
	 * Создаем воркера из тасков, запускаем воркера в 1 поток, по id конретной очереди.
	 * Пример: php run.php tasks-processor run-worker-debug --queue-id=2
	 */
	public function actionRunWorkerDebug()
	{
		$queueId    = isset($this->arguments['queue-id']) ? $this->arguments['queue-id'] : null;
		if($queueInfo   =   $this->application->bll->queue->getQueue($queueId))
		{
			$this->log('Running queue ' . $queueInfo['name'] . ' without workers limit in debug mode');
			$workerId   = $this->application->bll->queue->getRandomWorkerIdByQueueId($queueId);
			if(!$workerId)
			{
				$workerId = $this->createWorker($queueId, $queueInfo);
			}
			if ($workerId)
			{
				$this->workerProcess($workerId);
			}
		}
	}


	private function createWorker($queueId, array $queueInfo)
	{
		list($workerId, $tasksInWorker)    = $this->application->bll->queue->moveTasksToWorker(
			$queueId,
			$queueInfo['tasks_per_worker']
		);
		if($workerId)
		{
			$this->log('created worker for queue ' . $queueInfo['name'] . ', workerId:' . $workerId . ', ' . $tasksInWorker . '/' . $queueInfo['tasks_per_worker'] . ' tasks');
		}
		else
		{
			$this->log('queue ' . $queueInfo['name'] . ' has no tasks');
		}

		return $workerId;
	}
}
