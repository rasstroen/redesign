<?php

$modules = array(
	/**
	 * Меню в шапке на главной
	 */
	'top_menu_index' => array(
		'className' => '\Application\Module\Menu\Top',
		'template'  => 'menu',
		'action'    => 'list',
		'mode'      => 'index'
	),
	/**
	 * Меню в админке
	 */
	'admin_menu_index'=> array(
		'className' => '\Application\Module\Menu\Top',
		'template'  => 'menu',
		'action'    => 'list',
		'mode'      => 'admin'
	),
	/**
	 * Список рубрик в админке
	 */
	'admin_rubrics_list'=> array(
		'className' => '\Application\Module\Rubric',
		'template'  => 'admin',
		'action'    => 'list',
		'mode'      => 'rubrics'
	),
);

return array(
	'map'  => array(
		''      => 'index',
		/**
		 * управление сайтом
		 */
		'admin'  => array(
			''  => 'admin',
			'rubricator'	=> array(
				'' 		=> 'admin_rubrics',
				'%d'	=> 'admin_rubric'
			)
			),

	),
	'pages' => array(
		/**
		 * Главная страница администрирования
		 */
		'admin' => array(
			'layout'    => 'admin',
			'title'     => 'Администрирование',
			'blocks'    => array(
				'header'   => array(
					$modules['admin_menu_index'],
				),
				'content'   => array(
					//$modules['posts/lists/main'],
				),
				'sidebar'   => array(
					//$modules['ads/google/sidebar'],
				)
			)
		),
		/**
		 * Раздел управлнеия рубриками
		 */
		'admin_rubrics' => array(
			'layout'    => 'admin',
			'title'     => 'Управление рубрикатором',
			'blocks'    => array(
				'header'   => array(
					$modules['admin_menu_index'],
				),
				'content'   => array(
					$modules['admin_rubrics_list'],
				),
				'sidebar'   => array(
					//$modules['ads/google/sidebar'],
				)
			)
		),
		/**
		 * Главная страница
		 */
		'index'=> array(
			'layout'    => 'index',
			'title'     => 'Популярные записи. Самый быстрый ЖЖ Топ — Рейтинг записей Живого Журнала',
			'blocks'    => array(
				'header'   => array(
					$modules['top_menu_index'],
				),
				'content'   => array(
					//$modules['posts/lists/main'],
				),
				'sidebar'   => array(
					//$modules['ads/google/sidebar'],
				)
			)
		),
	)
);