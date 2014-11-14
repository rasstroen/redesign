<?php
namespace Application\Command;

use Application\BLL\Queue;

class Author extends Base
{
	/**
	 * сколько авторов пойдут в очередь на обновление
	 */
	const AUTHORS_FOR_INFO_FULL_UPDATE_IN_TASK = 60;

	/**
	 * Наполняем очередь для авторов, по которым пора вытащить подробную информацию.
	 * Мы постоянно получаем короткую информацию об авторах у Яндекса.
	 * Дополнительно нужно вытащить полную информацию - аватар, тайтл журнала и т.п.
	 */
	public function actionFillFullUpdateQueue()
	{
		$authorIds  = $this->application->bll->author->getIdsByOldestInfoFullUpdate(self::AUTHORS_FOR_INFO_FULL_UPDATE_IN_TASK);
		/**
		 * Только 1 таск
		 */
		$taskId = 'unique';
		$this->application->bll->queue->addTask(
			Queue::QUEUE_AUTHOR_FETCH_ALL_INFO,
			$taskId,
			$authorIds
		);
	}
}