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
		'mode'      => 'adminRubrics'
	),
	/**
	 * Редактирование рубрики
	 */
	'admin_rubric_edit'=> array(
		'className' => '\Application\Module\Rubric',
		'template'  => 'admin',
		'action'    => 'edit',
		'mode'      => 'item'
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
			'rubric'	=> array(
				'' 		=> 'admin_rubrics',
				'%d'	=> array(
					'_var'  => 'rubricId',
					''  => 'admin_rubric',
					'edit'  => 'admin_rubric_edit'
					)
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
		),
		/**
		 * Раздел управлнеия рубриками
		 */
		'admin_rubrics' => array(
			'layout'    => 'admin',
			'title'     => 'Управление рубрикатором',
			'css' => array(
				'/static/css/admin1.css'
			),
			'blocks'    => array(
				'content'   => array(
					'admin_rubrics_list'=>$modules['admin_rubrics_list'],
				),
			)
		),
		/**
		 * Редактирование рубрики
		 */
		'admin_rubric_edit' => array(
			'layout'    => 'admin',
			'title'     => 'Управление рубрикой',
			'blocks'    => array(
				'content'   => array(
					'admin_rubric_edit'=>$modules['admin_rubric_edit'],
				),
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
					'top_menu_index'=>$modules['top_menu_index'],
				),
			)
		),
	),
	/**
	 * Умолчания для лайаутов
	 */
	'layouts' => array(
		/**
		 * Умолчания для админки
		 */
		'admin' => array(
			'css' => array(
				'admin' => '/static/css/layout/admin.css',
			),
			'blocks'    => array(
				'header'   => array(
					'admin_menu_index'=>$modules['admin_menu_index'],
				),
			)
		)
	)
);