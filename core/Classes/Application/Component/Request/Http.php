<?php
namespace Application\Component\Request;

use Application\Component\Base;

class Http extends Base
{
	private $userAgent = 'http://lj-top.ru, amuhc@ya.ru lj-top.ru grabber';
	public function get($url, $timeout = 10)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout + 10);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}