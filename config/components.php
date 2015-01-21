<?php

return array(
	'controller'    => array(
		'className' => '\Application\Component\Controller\Web',
	),
	'request'       => array(
		'className' => '\Application\Component\Request\Web',
	),
	'routing'       => array(
		'className' => '\Application\Component\Routing\Web',
	),
	'view'          => array(
		'className' => '\Application\Component\View\Web',
	),
	'bll'           => array(
		'className' => '\Application\BLL\Factory',
	),
	'db'           => array(
		'className' => '\Application\Component\Database\Base',
	),
	'urlManager'    => array(
		'className' => '\Application\Component\Routing\UrlManager',
	),
	'imageConverter'    => array(
		'className' => '\Application\Component\Image\Converter',
	),
	'httpRequest'       => array(
		'className' => '\Application\Component\Request\Http',
	),
	'youtube'       => array(
		'className' => '\Application\Component\Youtube',
	),
);