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
	 * �� URL ����� �������� ������ "�����", ���������� � ���� ���������� � ����� � �����
	 *
	 * @param string $videoUrl
	 *
	 * @return YoutubeVideo ������ � �������� �� ��� ������ ���� ������ ���
	 */
	public function getByUrl($videoUrl)
	{
		/**
		 * ���������� id �� ������������ URL
		 */
		$videoId = $this->getYoutubeIdByURL($videoUrl);
		/**
		 * �������� ����� ��� � �������� ������
		 */
		$apiData = $this->getApiVideoData($videoId);

		/**
		 * ���������� ��������� ������ Torg_Component_Youtube_Video
		 */

		return new YoutubeVideo($videoId, $apiData);
	}

	/**
	 * ������ URL � �������� �� ���� id �����
	 * ���������� URL
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
	 * �������� ����� $method ��� ����� � ����������� $params
	 *
	 * @param string $method
	 * @param array  $params
	 *
	 * @return array ������������� ������ - ������ �������� ��� �����
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
	 * @return array ����� ������ ���, ���������� ������ � �����������
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