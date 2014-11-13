<?php
namespace Application\Command\Tasks\Worker;

class Author extends Base
{
	private $fields = array(
		'username'              => 1,// ppb_username
		'userinfo_change_time'  => 1,// last change time
		'is_community'          => 1,// is it community profile
		'url'                   => 1,// livejournal url
	);

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
		foreach($authorInfo as $field => $value)
		{
			if(isset($this->fields[$field]))
			{
				$data[$field] = $value;
			}
		}

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
				$this->application->bll->author->updateInfoByUserName(
					$authorInfo['username'],
					$data
				);
			}
		}
		else
		{
			$this->application->bll->author->insert(
				$authorInfo['username'],
				$data
			);
		}
	}
}
