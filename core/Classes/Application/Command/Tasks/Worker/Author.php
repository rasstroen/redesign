<?php
namespace Application\Command\Tasks\Worker;

use Application\BLL\Queue;

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
		 * Тащим его записи
		 */
		$posts  = $this->fetchPosts($authorInfo['url']);

		if(count($posts))
		{
			$this->log('adding task to process ' . count($posts) . ' posts');

			$this->application->bll->queue->addTask(
				Queue::QUEUE_POSTS_PROCESS_POSTS,
				$authorInfo['username'],
				array(
					'posts'     => $posts,
					'username'  => $authorInfo['username']
				)
			);

			$this->log('adding task to process ' . $authorInfo['username'] . ' after week');

			$this->application->bll->queue->addTask(
				Queue::QUEUE_AUTHOR_FETCH_RSS,
				$authorInfo['username'],
				$authorInfo,
				24*60*60*7
			);

		}
	}

	public function fetchPosts($url)
	{
		$url .= 'data/rss';

		$content    = $this->application->httpRequest->getWithCache($url, 3600);
		$xml = xml_parser_create();
		xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE,1);
		xml_parse_into_struct($xml, $content, $values, $index);
		xml_parser_free($xml);

		$posts = array();

		foreach($index['ITEM'] as $key => $itemIndex)
		{
			$currentPost = array();
			for($valuesKey = $itemIndex; $valuesKey<$index['ITEM'][$key+1]; $valuesKey++)
			{
				$postValues = $values[$valuesKey];
				switch($postValues['tag'])
				{
					case 'GUID':
					{
						$currentPost['url'] = $postValues['value'];
					}
					case 'PUBDATE':case 'TITLE': case 'LINK': case 'DESCRIPTION': case 'COMMENTS':
					{
						$currentPost[strtolower($postValues['tag'])] = $postValues['value'];
						break;
					}
					case 'LJ:REPLY-COUNT': {
						$currentPost['comments'] = $postValues['value'];
						break;
					}
					case 'CATEGORY': {
						$currentPost['tags'][$postValues['value']] = $postValues['value'];
						break;
					}
				}
			}
			if($currentPost['comments']) {
				$posts[] = $currentPost;
			}
		}

		return $posts;
	}

	public function methodFetchFullInfo(array $authorIds)
	{
		$authors = $this->application->bll->author->getByIds($authorIds);

		$this->log('full update for: ' . print_r($authorIds, 1));
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
				else
				{
					$authorInfo = array(
						'userinfo_full_change_time' => time()
					);
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
			$data['journal_city_name'] = urldecode($values[$index['YA:CITY'][0]]['attributes']['DC:TITLE']);
		}
		$data['journal_created']            = strtotime($values[$index['FOAF:WEBLOG'][0]]['attributes']['LJ:DATECREATED']);
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
