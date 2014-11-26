<?php
namespace Application\Component\Request;

use Application\Component\Base;

class Http extends Base
{
	private $logFile    = '/tmp/lj-top.ru.curl_requests';
	private $userAgent  = 'http://lj-top.ru, amuhc@ya.ru lj-top.ru grabber';

	public function getWithCache($url, $cacheTime, $timeout = 10)
	{
		$fileName = '/tmp/cache/getWCache_'.md5($url);
		if(file_exists($fileName))
		{
			if(time() - fileatime($fileName) < $cacheTime)
			{
				return file_get_contents($fileName);
			}
		}

		$content = $this->get($url, $timeout);
		file_put_contents($fileName, $content);
		return $content;
	}

	public function get($url, $timeout = 10)
	{
		$start  = microtime(true);
		$ch     = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout + 10);
		$result = curl_exec($ch);
		$end    = microtime(true);
		curl_close($ch);
		$f      = fopen($this->logFile, "a");
		fwrite($f, date('Y-m-d H:i:s') . ' ' . $url . ' ' . ($end - $start) . ', ' . strlen($result) . "\n");
		fclose($f);
		return $result;
	}
}