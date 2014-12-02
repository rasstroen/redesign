<?php
namespace Application\Command;

use Application\BLL\Queue;

class Author extends Base
{
	/**
	 * сколько авторов пойдут в очередь на обновление
	 */
	const AUTHORS_FOR_INFO_FULL_UPDATE_IN_TASK = 200;

	/**
	 * Пересчитываем позиции авторов в соответствии с рейтингом активных постов в топе
	 *
	 * @param int $authorType
	 * @throws \Exception
	 */
	public function actionCalculateRating($authorType = \Application\BLL\Author::AUTHOR_TYPE_USER)
	{
		$authors    = $this->application->bll->author->getTopQueryUse($authorType);
		$allAuthors = array();
		while($author = $authors->fetch(\PDO::FETCH_ASSOC))
		{
			$allAuthors[$author['author_id']] = $author['rating'];
		}


		arsort($allAuthors);


		$chunks = array_chunk($allAuthors, 100 , true);

		$ratingAuthors = array();
		foreach($chunks as $chunk)
		{
			$ratings	= $this->application->bll->author->getCurrentAuthorRatings(array_keys($chunk));

			foreach($chunk as $authorId => $rating)
			{
				if(isset($ratings[$authorId]))
				{
					$ratingAuthors[$authorId] = $ratings[$authorId]['rating'];
				}
			}
		}

		arsort($ratingAuthors);


		if($authorType == \Application\BLL\Author::AUTHOR_TYPE_USER)
		{
			$this->log('Creating author_temp');
			$this->application->db->master->query('CREATE TABLE IF NOT EXISTS `author_temp` LIKE `author`');
			$this->log('Truncating author_temp');
			$this->application->db->master->query('TRUNCATE `author_temp`');
			$this->application->db->master->query('ALTER TABLE `author_temp` DISABLE KEYS');
			$this->log('COPY all authors from author to author_temp');
			$this->application->db->master->query('INSERT INTO `author_temp`( SELECT * FROM `author`)');
		}


		$chunks = array_chunk($ratingAuthors, 100 , true);

		$position = 1;
		$this->log('Start updating');
		foreach($chunks as $chunk)
		{
			foreach($chunk as $authorId => $rating)
			{
				$this->application->bll->author->updateById($authorId,
					array(
						'rating_last_position'  => $rating,
						'position'              => $position
					), 'author_temp'
				);
				$position++;
			}
		}
		$this->log('last position is ' . $position);


		if($authorType == \Application\BLL\Author::AUTHOR_TYPE_USER)
		{
			$this->log('Calculating communities rating');
			$this->actionCalculateRating(\Application\BLL\Author::AUTHOR_TYPE_COMMUNITY);
		}
		else
		{
			$this->application->db->master->query('ALTER TABLE `author_temp` ENABLE KEYS');
			$this->log('replacing tables');
			$this->application->db->master->query('RENAME TABLE `author` to `author_remove`, `author_temp` to `author`');
			$this->log('deleting tables');
			$this->application->db->master->query('DROP TABLE `author_remove`');
		}
	}
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
			true
		);
	}
}