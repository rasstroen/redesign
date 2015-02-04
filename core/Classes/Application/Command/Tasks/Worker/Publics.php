<?php
namespace Application\Command\Tasks\Worker;

use Application\BLL\Queue;

class Publics extends Base
{
	private $apiId = 2727212;
	private $secretKey = '6iwNXO1Y68v0jnIg9tmC';

	public function methodGrab(array $data)
	{
		$publicId = $data['id'];
		$this->log('Grabbing public: ' . $publicId);

		$public = $this->application->bll->public->getById($publicId);
		$this->log(print_r($public, true));

		$perQuery = max(20, $public['per_query']);
		$offset   = $public['offset'];
		$offset -= $perQuery * 2;
		$offset = max(0, $offset);

		$lastPostDate = 0;
		do
		{
			$vkApi = new vkapi(
				$this->apiId,
				$this->secretKey
			);

			$resp  =
				$vkApi->api(
					'photos.get',
					array(
						'extended' => 1,
						'gid'      => $publicId,
						'aid'      => 'wall',
						'limit'    => max(10, min(500, $public['per_query'])),
						'offset'   => $offset
					)
				);
			$posts = $resp['response'];
			$offset += count($posts);
			$this->application->bll->public->savePosts($public, $posts, 'wall');
			foreach($posts as $post)
			{
				$lastPostDate = max($lastPostDate, date('Y-m-d H:i:s', $post['created']));
			}
		}
		while(count($posts));

		$this->log('Adding public to fetch:' . $public['id']);
		$taskId = $public['id'];

		$this->application->bll->queue->addTask(
			Queue::QUEUE_PUBLIC_FETCH_PUBLIC,
			$taskId,
			array('id' => $public['id']),
			5 * 60 * 60,
			true
		);
		if($lastPostDate)
		{
			$this->log('saving offest ' . $offset . ' last ' . $lastPostDate . ' for public' . $publicId);
			$this->application->bll->public->setLastPostDate($publicId, $lastPostDate, $offset);
		}
	}
}


class vkapi
{

	var $api_secret;
	var $app_id;
	var $api_url;

	function __construct($app_id, $api_secret, $api_url = 'api.vk.com/api.php')
	{
		$this->app_id     = $app_id;
		$this->api_secret = $api_secret;
		if(!strstr($api_url, 'http://'))
		{
			$api_url = 'http://' . $api_url;
		}
		$this->api_url = $api_url;
	}

	function api($method, $params = array())
	{
		usleep(10000);
		if(!$params)
		{
			$params = array();
		}
		$params['api_id']    = $this->app_id;
		$params['v']         = '3.0';
		$params['method']    = $method;
		$params['timestamp'] = time();
		$params['format']    = 'json';
		$params['random']    = rand(0, 10000);
		ksort($params);
		$sig = '';
		foreach($params as $k => $v)
		{
			$sig .= $k . '=' . $v;
		}
		$sig .= $this->api_secret;
		$params['sig'] = md5($sig);
		$query         = $this->api_url . '?' . $this->params($params);
		$res           = file_get_contents($query);

		$x = json_decode($res, true);
		if($res && !$x)
		{
			return -1;
		}

		return $x ? $x : array();
	}

	function params($params)
	{
		$pice = array();
		foreach($params as $k => $v)
		{
			$pice[] = $k . '=' . urlencode($v);
		}

		return implode('&', $pice);
	}
}
