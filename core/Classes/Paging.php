<?php
namespace Classes;

use Application\Web;

class Paging {
	/**
	 * @var Web
	 */
	private $application;
	function __construct($page, $perPage, $itemsCount, $application)
	{
		$this->page         = $page;
		$this->perPage      = $perPage;
		$this->itemsCount   = $itemsCount;
		$this->application = $application;
	}

	function getOffset()
	{
		return ($this->page-1) * $this->perPage;
	}

	function getLimit()
	{
		return $this->perPage;
	}

	function getPages()
	{
		$pages = array();
		for($i = 1; $i < ceil($this->itemsCount/ $this->perPage)+1; $i++)
		{
			$pages[$i] = array(
				'title' => $i,
				'url'   => $this->application->urlManager->getCurrentUrl(
					array(
						'p' => $i
					)
				)
			);
		}
		return array(
			'page'  => $this->page,
			'pages' => $pages
		);
	}
}