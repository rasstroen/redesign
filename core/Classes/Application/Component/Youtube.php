<?php
namespace Application\Component;

class Youtube extends Base
{

	private $apiKey = 'AIzaSyAqdxR3Ew9_sh1rv7WO8XCCuSKrVGKS6_8';

	private $apiUrl = 'https://www.googleapis.com/youtube/v3/';

	/**
	 * @param string $apiKey
	 */
	public function setApiKey($apiKey)
	{
		$this->apiKey = $apiKey;
	}

	/**
	 * По URL видео получаем объект "Видео", содержащий в себе информацию о видео с ютуба
	 *
	 * @param string $videoUrl
	 *
	 * @return YoutubeVideo Объект с гетерами на все важные поля ответа апи
	 */
	public function getByUrl($videoUrl)
	{
		/**
		 * определяем id по запрошенному URL
		 */
		$videoId = $this->getYoutubeIdByURL($videoUrl);
		/**
		 * Вызываем метод апи и получаем данные
		 */
		$apiData = $this->getApiVideoData($videoId);

		/**
		 * Возвращаем экземпляр класса Torg_Component_Youtube_Video
		 */

		return new YoutubeVideo($videoId, $apiData);
	}

	/**
	 * Парсим URL и забираем из него id видео
	 * Правильные URL
	 * http://www.youtube.com/watch?v=85aoB9aVglk
	 * ?v=85aoB9aVglk
	 * youtube.com/?v=85aoB9aVglk
	 *
	 * @param string $videoURL
	 *
	 * @return mixed
	 */
	private function getYoutubeIdByURL($videoURL)
	{
		$id = null;
		parse_str(parse_url($videoURL, PHP_URL_QUERY), $query);
		$id = isset($query['v']) ? $query['v'] : array_shift(array_keys($query));
		if(!$id)
		{
			throw new \Exception('Unable to get video id by URL: ' . $videoURL);
		}

		return $id;
	}


	/**
	 * Вызываем метод $method АПИ Ютуба с параметрами $params
	 *
	 * @param string $method
	 * @param array  $params
	 *
	 * @return array ассоциативный массив - дерево респонса АПИ ютуба
	 * @throws Exception
	 */
	private function callMethod($method, array $params = array())
	{
		$url           = $this->apiUrl;
		$params['key'] = $this->apiKey;
		$url .= $method . '?' . http_build_query($params);
		try
		{
			$apiResponse = $this->application->httpRequest->getWithCache($url, 60 * 60);
		}
		catch(\Exception $e)
		{
			throw new \Exception('Cant get YouTube API response while calling ' . $url);
		}
		$array = json_decode($apiResponse, true);
		if(!is_array($array) || !isset($array['kind']))
		{
			throw new \Exception('Got something weird from youtube while calling ' . $url);
		}

		return $array;
	}

	/**
	 * @param string $videoId
	 *
	 * @return array кусок ответа АПИ, содержащий данные о видеоролике
	 * @throws Exception
	 */
	private function getApiVideoData($videoId)
	{
		if(!$videoId)
		{
			throw new \Exception('Use setURL() function to set video URL before fetching data');
		}
		$params = array('id' => $videoId, 'part' => 'snippet,contentDetails,status');
		$data   = $this->callMethod('videos', $params);
		if(isset($data['items'][0]))
		{
			$apiData = $data['items'][0];
		}
		else
		{
			throw new \Exception('Illegal Youtube API answer while getting API response');
		}

		return $apiData;
	}
}