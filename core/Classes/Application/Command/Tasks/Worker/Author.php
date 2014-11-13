<?php
namespace Application\Command\Tasks\Worker;

class Author extends Base
{
	/**
	 * Забираем свежие записи автора
	 *
	 * @param array $authorInfo
	 */
	public function methodFetchRss(array $authorInfo)
	{
		/**
		 * Находим автора
		 */
		$this->log($authorInfo['username'] . ' has url:' . $authorInfo['url']);
		/**
		 * Тащим его ленту
		 */

		/**
		 * Собираем посты в массив
		 */

		/**
		 * Для каждого поста - ставим задание на обработку поста
		 */

		/**
		 * Если нет автора - ставим таску на создание автора
		 */
	}

	/**
	 * Обновляем информацию об авторе
	 * @param array $authorInfo
	 */
	public function methodUpdateInfo(array $authorInfo)
	{
		$author     = $this->application->bll->author->getByUserName($authorInfo['username']);
		$data       = array();

		if($author)
		{
			$needSave = false;
			foreach($data as $field => $value)
			{
				if($author[$field] != $value)
				{
					$needSave   = true;
				}
			}
			if($needSave)
			{
				$data['userinfo_change_time'] = time();
				$this->application->bll->author->updateInfoByUserName(
					$authorInfo['username'],
					$data
				);
			}
		}
		else
		{
			$data['userinfo_change_time'] = time();
			$this->application->bll->author->insert(
				$authorInfo['username'],
				$data
			);
		}
	}
}
