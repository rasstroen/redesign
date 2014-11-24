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

	/**
	 * Просмотр рубрики
	 */
	'admin_rubric'=> array(
		'className' => '\Application\Module\Rubric',
		'template'  => 'admin',
		'action'    => 'show',
		'mode'      => 'item'
	),
	/**
	 * Состояние демонов
	 */
	'admin_demons'=> array(
		'className' => '\Application\Module\Admin\Demons',
		'template'  => 'admin',
		'action'    => 'show',
		'mode'      => 'demons'
	),
	/**
	 * Топ авторов
	 */
	'authors_top'=> array(
		'className' => '\Application\Module\Author',
		'template'  => 'author',
		'action'    => 'list',
		'mode'      => 'top'
	),
	/**
	 * топ популярных постов на главной
	 */
	'index_top_popular'=> array(
		'className' => '\Application\Module\Post',
		'template'  => 'post',
		'action'    => 'list',
		'mode'      => 'indexTopPopular'
	),
	/**
	 * топ новых на главной
	 */
	'index_top_new'=> array(
		'className' => '\Application\Module\Post',
		'template'  => 'post',
		'action'    => 'list',
		'mode'      => 'indexTopNew'
	),

);

return array(
	'map'  => array(

		'top' => array(
			'month' => array(
				'authors' => array(
					'' => 'authors_top',
				)
			)
		),
		/**
		 * Главная страница
		 */
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
			),
			'demons' => array(
				''  => 'admin_demons'
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
			'blocks'    => array(
				'content'   => array(
					'admin_rubrics_list'=>$modules['admin_rubrics_list'],
				),
			)
		),
		/**
		 * Демоны
		*/
		'admin_demons' => array(
			'layout' => 'admin',
			'title' => 'Демоны',
			'blocks' => array(
				'content' => array(
					'admin_demons' => $modules['admin_demons'],
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
		 * Просмотр рубрики в админке
		 */
		'admin_rubric' => array(
			'layout'    => 'admin',
			'title'     => 'Рубрика',
			'blocks'    => array(
				'content'   => array(
					'admin_rubric'=>$modules['admin_rubric'],
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
				'content' => array(
					'index_top_popular'=>$modules['index_top_popular'],
					'index_top_new'=>$modules['index_top_new'],
				)
			)
		),
		/**
		 * Топ авторов
		 */
		'authors_top'=> array(
			'layout'    => 'index',
			'title'     => 'Популярные записи. Самый быстрый ЖЖ Топ — Рейтинг записей Живого Журнала',
			'blocks'    => array(
				'header'   => array(
					'top_menu_index'=>$modules['top_menu_index'],
				),
				'content' => array(
					'authors_top'=>$modules['authors_top'],
				)
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
				'reset' => '/static/css/reset.css',
				'admin' => '/static/css/layout/admin.css',
			),
			'blocks'    => array(
				'header'   => array(
					'admin_menu_index'=>$modules['admin_menu_index'],
				),
			)
		),
		'index' => array(
			'css' => array(
				'reset' => '/static/css/reset.css',
				'index' => '/static/css/layout/index.css',
				'posts' => '/static/css/posts.css',
			),
			'blocks'    => array(
				'header'   => array(
					'top_menu_index'=>$modules['top_menu_index'],
				),
			)
		)
	)
);