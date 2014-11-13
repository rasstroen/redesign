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
	 *
	 */
	public function methodFetchFullInfo(array $authorIds)
	{
		foreach($authorIds as $authorId)
		{
			$this->log('Fetching userId:' . $authorId);
		}
	}

	/**
	 * Обновляем информацию об авторе
	 * @param array $authorInfo
	 */
	public function methodUpdateInfo(array $authorInfo)
	{
		$author     = $this->application->bll->author->getByUserName($authorInfo['username']);

		if($author)
		{
			$needSave = false;
			foreach($authorInfo as $field => $value)
			{
				if(isset($author[$field]) && ($author[$field] != $value))
				{
					$needSave   = true;
				}
			}
			if($needSave)
			{
				$authorInfo['userinfo_change_time'] = time();
				$this->application->bll->author->updateInfoByUserName(
					$authorInfo['username'],
					$authorInfo
				);
			}
		}
		else
		{
			$authorInfo['userinfo_change_time'] = time();
			$this->application->bll->author->insert(
				$authorInfo['username'],
				$authorInfo
			);
		}
	}
}
