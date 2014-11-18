<?php
namespace Application\Command;

use Application\BLL\Queue;

class Author extends Base
{
	/**
	 * сколько авторов пойдут в очередь на обновление
	 */
	const AUTHORS_FOR_INFO_FULL_UPDATE_IN_TASK = 20;

	/**
	 * Наполняем очередь для авторов, по которым пора вытащить подробную информацию.
	 * Мы постоянно получаем короткую информацию об авторах у Яндекса.
	 * Дополнительно нужно вытащить полную информацию - аватар, тайтл журнала и т.п.
	 */
	public function actionFillFullUpdateQueue()
	{
		/**
		 * Если запущен воркер обновления авторов - засыпаем обратно
		 */
		$worker = $this->application->bll->queue->getRandomWorkerIdByQueueId(Queue::QUEUE_AUTHOR_FETCH_ALL_INFO);
		if(count($worker))
		{
			$this->log('waiting for existing worker');
			return;
		}

		$authorIds  = $this->application->bll->author->getIdsByOldestInfoFullUpdate(self::AUTHORS_FOR_INFO_FULL_UPDATE_IN_TASK);
		$this->log(count($authorIds) . ' authors added for full update: ' . print_r($authorIds, 1));
		/**
		 * Только 1 таск
		 */
		$taskId = 'unique';
		$this->application->bll->queue->addTask(
			Queue::QUEUE_AUTHOR_FETCH_ALL_INFO,
			$taskId,
			$authorIds,
			0,
			false
		);
	}
}