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

	public function methodFetchFullInfo(array $authorIds)
	{
		$authors = $this->application->bll->author->getByIds($authorIds);

		foreach($authorIds as $authorId)
		{
			if(isset($authors[$authorId]))
			{
				$author     = $authors[$authorId];
				$this->log($author['username'].', url: ' . $author['url']);
				$authorInfo = $this->getFoaf($author['url']);

				if(intval($authorInfo['journal_posted']) > 0)
				{
					$authorInfo['userinfo_full_change_time'] = time();
					$this->application->bll->author->updateInfoByUserName(
						$author['username'],
						$authorInfo
					);
				}
			}
		}
	}

	private function getFoaf($url)
	{
		$url .='data/foaf';
		
		$content    = $this->application->httpRequest->get($url);
		$xml = xml_parser_create();
		xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE,1);
		xml_parse_into_struct($xml, $content, $values, $index);
		xml_parser_free($xml);

		$data = array();

		$data['journal_posted']             = $values[$index['YA:POSTED'][0]]['value'];
		$data['journal_commented']          = $values[$index['YA:POSTED'][1]]['value'];
		$data['journal_comments_received']  = $values[$index['YA:RECEIVED'][0]]['value'];
		if(isset($index['LJ:JOURNALTITLE']))
		{
			$data['journal_title'] = $values[$index['LJ:JOURNALTITLE'][0]]['value'];
		}
		elseif(isset($index['FOAF:NAME']))
		{
			$data['journal_title'] = $values[$index['FOAF:NAME'][0]]['value'];
		}
		if(isset($index['LJ:JOURNALSUBTITLE']))
		{
			$data['journal_subtitle']           = $values[$index['LJ:JOURNALSUBTITLE'][0]]['value'];
		}
		if(isset($index['YA:COUNTRY']))
		{
			$data['journal_country_code'] = $values[$index['YA:COUNTRY'][0]]['attributes']['DC:TITLE'];
		}
		if(isset($index['YA:CITY']))
		{
			$data['journal_city_name'] = $values[$index['YA:CITY'][0]]['attributes']['DC:TITLE'];
		}
		$data['journal_created']            = $values[$index['FOAF:WEBLOG'][0]]['attributes']['LJ:DATECREATED'];
		if(isset($index['YA:BIO']))
		{
			$data['journal_bio'] = $values[$index['YA:BIO'][0]]['value'];
		}
		if(isset($index['FOAF:IMG']))
		{
			$data['journal_pic']                = $values[$index['FOAF:IMG'][0]]['attributes']['RDF:RESOURCE'];
		}
		$data['is_community']                   = isset($index['FOAF:GROUP']) ? 1: 0;
		return $data;
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
