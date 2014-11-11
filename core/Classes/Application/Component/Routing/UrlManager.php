<?php
namespace Application\Component\Routing;
use Application\Component\Base;

class UrlManager extends Base
{
	public function getCurrentUrl(array $parameters = array())
	{
		$params = $this->application->request->getQueryParams();
		foreach($parameters as $param => $value)
		{
			$params[$param] = $value;
		}
		$url = $this->application->request->getUrl();
		list($url, $get) = explode('?' , $url);
		return $url .'?' . http_build_query($params);
	}
}