<?php
namespace Application\Command;

class Post extends Base
{
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