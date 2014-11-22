<?php
namespace Application\Command;

use Application\BLL\Posts;

class Post extends Base
{
	const MAX_POSTS_IN_ACTIVE   = 1024;
	const MAX_POSTS_IN_NEW      = 512;
	public function actionRebuildActivePosts()
	{
		$date           = new \DateTime();
		$interval       = new \DateInterval('P' . Posts::POST_ACTIVE_LIFE_DAYS . 'D');
		$currentMonth   = $date->format('Y_m');
		$previousMonth  = $date->sub($interval)->format('Y_m');

		$months[$currentMonth]  = $currentMonth;
		$months[$previousMonth] = $previousMonth;
		$minPubTime = time() - 24 * 60 * 60 * Posts::POST_ACTIVE_LIFE_DAYS;
		foreach($months as $month)
		{
			$this->log('getting all posts from ' . date('Y-m-d H:i:s', $minPubTime) . ' from table ' . $month);
			/**
			 * Забираем все посты
			 */

			$posts = $this->application->bll->posts->getByPeriodFromDateTable($minPubTime, time(), $month);
			foreach($posts as &$post)
			{
				$dateCoefficient = 1;
					$hoursLeft = ceil((time() - $post['pub_time']) / 60 / 60);
					if ($hoursLeft > (24 * 5))
					{
						$dateCoefficient *= 0.2;
					}
					elseif($hoursLeft > (24 * 3))
					{
						$dateCoefficient *= 0.3;
					}
					elseif($hoursLeft > 24*2)
					{
						$dateCoefficient *= 0.5;
					}
					elseif ($hoursLeft >= 24)
					{
						$dateCoefficient *= 0.9;
					}
					elseif ($hoursLeft > 12)
					{
						$dateCoefficient *= 1;
					}

				$post['rating'] = $dateCoefficient * (ceil($post['comments'] / 35) * 80);
				$post['coef']   = $dateCoefficient;
				$post['date']   = date('Y-m-d H:i:s', $post['pub_time']);
			}
			unset($post);
			uasort($posts, function($a, $b)
			{
				return $b['rating'] - $a['rating'];
			});
			/**
			 * Просчитываем рейтинг
			 */
		}

		$postsToInsert = array_slice($posts, 0 , self::MAX_POSTS_IN_ACTIVE, true);


		uasort($posts, function($a, $b)
		{
			return $b['pub_time'] - $a['pub_time'];
		});

		$postsToInsertNewest = array_slice($posts, 0 , self::MAX_POSTS_IN_NEW , true);
		$this->application->db->master->query('ALTER TABLE active_posts DISABLE KEYS');
		foreach($postsToInsert as $post)
		{
			$this->application->bll->posts->savePostToActive($post['post_id'], $post['author_id'], $post);
		}

		foreach($postsToInsertNewest as $post)
		{
			$this->application->bll->posts->savePostToActive($post['post_id'], $post['author_id'], $post);
		}
		$this->application->db->master->query('ALTER TABLE active_posts ENABLE KEYS');
	}
	/**
	 * Парсим выдачу Яндекса - вытаскиваем свежие записи
	 */
	public function actionFetchYandex()
	{
		$pageIndex      = 1;
		$processedCount = 0;

		do
		{
			$items = $this->parseEntriesApiPage($pageIndex);
			foreach($items as $item)
			{
				if($this->processEntriesApiItem($item))
				{
					$processedCount++;
				}
			}
			$pageIndex++;
		}
		while(count($items));
		$this->log('total ' . $processedCount . ' items processed');
	}

	private function processEntriesApiItem(array $item)
	{
		return $this->application->bll->posts->processYandexApiData($item);
	}

	private function parseEntriesApiPage($pageIndex)
	{
		$items  = array();
		$url    = 'http://blogs.yandex.ru/entriesapi/?p=' . $pageIndex;
		$content    = $this->application->httpRequest->get($url);

		$xml = xml_parser_create();
		xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE,1);
		xml_parse_into_struct($xml, $content, $values, $index);
		xml_parser_free($xml);

		$fieldsOffset = array(
			'title'                 => 2,
			'author'                => 0,
			'link'                  => 2,
			'pubDate'               => 0,
			'yablogs:comments'      => 0,
			'yablogs:comments24'    => 0,
			'yablogs:links'         => 0,
			'yablogs:links24'       => 0,
			'yablogs:linksweight'   => 0,
			'yablogs:ppb_username'  => 0,
			'yablogs:links24weight' => 0,
			'description'           => 1,
		);
		if(isset($index['PUBDATE']))
		{
			foreach ($index['PUBDATE'] as $indexIndex => $itemIndex)
			{
				foreach ($fieldsOffset as $field => $fieldOffset)
				{
					$fieldIndex                 = $index[strtoupper($field)][$indexIndex + $fieldOffset];
					$items[$indexIndex][$field] = isset($values[$fieldIndex]['value']) ? $values[$fieldIndex]['value'] : '';
				}
			}
		}
		$this->log($url . ', page #' . $pageIndex . ', ' . count($items) . ' items');
		return $items;
	}
}