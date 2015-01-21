<?php
namespace Application\Component;
class YoutubeVideo
{

	private $apiData = null;
	public $videoId = null;

	function __construct($videoId, array $apiData)
	{
		$this->id = $videoId;
		$this->apiData = $apiData;
	}

	/**
	 * ѕолучаем список thumbnails отсортированных по возрастанию размера
	 *
	 * @return array
	 */
	public function getThumbnails()
	{
		$thumbnails = array();
		$snippetPart = $this->apiData['snippet'];
		if (isset($snippetPart['thumbnails']))
		{
			foreach ($snippetPart['thumbnails'] as $key => $thumb)
			{
				if($thumb['url'])
				{
					$thumbnails[$key] = $thumb['url'];
				}
			}
		}
		return $thumbnails;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return isset($this->apiData['snippet']['title']) ?
			$this->apiData['snippet']['title'] : '';
	}

	/**
	 * @return string
	 */
	public function getYoutubeLink()
	{
		return 'http://www.youtube.com/watch?v='.$this->id;
	}

	/**
	 * ƒоступность дл€ публичного просмотра
	 *
	 * @return bool
	 */
	public function isPublic()
	{
		return isset($this->apiData['status']['privacyStatus']) ?
			($this->apiData['status']['privacyStatus'] === 'public') : false;
	}

	/**
	 * ¬озможность встраивани€
	 *
	 * @return bool
	 */
	public function isEmbeddable()
	{
		return isset($this->apiData['status']['embeddable']) ?
			(intval($this->apiData['status']['embeddable'])>0) : false;
	}

}