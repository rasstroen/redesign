<?php
namespace Application\BLL;
/**
 * Class Posts
 * @package Application\BLL
 */
class Posts extends BLL
{
	/**
	 * Обрабатываем данные из рейтинга яндекса - ставим задачи на обновление постов
	 * автора, задачу на добавление/обновление автора.
	 *
	 * @param array $item
	 */
	public function processYandexApiData(array $item)
	{
		preg_match('/http:\/\/(.*).livejournal.com\//isU', $item['author'], $matches);
		if(count($matches))
		{
			/**
			 * Это пользователь ЖЖ
			 * Добавляем задачу на обновление информации о пользователе
			 */
			$this->application->bll->queue->addTask(
				Queue::QUEUE_AUTHOR_UPDATE_INFO,
				$item['yablogs:ppb_username'],
				array(
					'username'      => $item['yablogs:ppb_username'],
					'url'           => $item['author'],
				)
			);
			/**
			 * Добавляем задачу на обновление ленты пользователя - если есть в выдаче яндекса,
			 * значит есть пост в топе, нужно его обновить прямо сейчас
			 */
			$this->application->bll->queue->addTask(
				Queue::QUEUE_AUTHOR_FETCH_RSS,
				$item['yablogs:ppb_username'],
				array(
					'username'      => $item['yablogs:ppb_username'],
					'url'           => $item['author'],
					'lastPostTime'  => strtotime($item['pubDate']),
				)
			);
			return true;
		}
		return false;
	}
}
