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
	'admin_themes' => array(
		'className' => '\Application\Module\Admin\Theme',
		'template'  => 'theme',
		'action'    => 'list',
		'mode'      => 'admin'
	),
	'admin_theme_item' => array(
		'className' => '\Application\Module\Admin\Theme',
		'template'  => 'theme',
		'action'    => 'show',
		'mode'      => 'item'
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
	/**
	 * топ новых на главной
	 */
	'post_item'=> array(
		'className' => '\Application\Module\Post',
		'template'  => 'post',
		'action'    => 'show',
		'mode'      => 'item'
	),
	/**
	 * Страница автора
	 */
	'author'=> array(
		'className' => '\Application\Module\Author',
		'template'  => 'author',
		'action'    => 'show',
		'mode'      => 'item'
	),
	/**
	 * привязки постов
	 */
	'admin_linked_posts'=> array(
		'className' => '\Application\Module\Rubric',
		'template'  => 'rubric',
		'action'    => 'list',
		'mode'      => 'adminLinkedPosts'
	),
	/**
	 * рубрики на главной
	 */
	'index_rubrics'=> array(
		'className' => '\Application\Module\Rubric',
		'template'  => 'rubric',
		'action'    => 'list',
		'mode'      => 'index'
	),
	'admin_theme_posts'=> array(
		'className' => '\Application\Module\Admin\Theme',
		'template'  => 'theme',
		'action'    => 'list',
		'mode'      => 'adminThemePosts'
	),
);

return array(
	'map'  => array(
		'top' => array(

				'authors' => array(
					'_var'  => 'authorType',
					'' => 'authors_top',
				),
				'community' => array(
					'_var'  => 'authorType',
					'' => 'authors_top',
			),
		),
		'author'    => array(
			'%s' => array(
				'_var'  => 'username',
				'' => 'author',
			)
		),
		'post'      => array(
			'%s' => array(
				'_var' => 'username',
				'%d' => array(
					'_var' => 'postId',
					'' => 'post_item',
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
					'edit'  => 'admin_rubric_edit',
					'linked'    => array(
						''      => 'admin_linked_posts',
						'done'  => array(
							''=>	'admin_linked_posts',
							'_var'  => 'isDone',
						),
					)
					)
			),
			'demons' => array(
				''  => 'admin_demons'
			),
			'theme' => array(
				''  => 'admin_themes',
				'%d'    => array(
					'_var'  => 'themeId',
					'' => 'admin_theme_item',
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
		 * Темы
		 */
		'admin_themes' => array(
			'layout' => 'admin',
			'title' => 'Темы',
			'blocks' => array(
				'content' => array(
					'admin_themes' => $modules['admin_themes'],
				),
			)
		),
		/**
		 * Управление темой
		 */
		'admin_theme_item' => array(
			'layout' => 'admin',
			'title' => 'Тема',
			'blocks' => array(
				'content' => array(
					'admin_theme_item' => $modules['admin_theme_item'],
					'admin_theme_posts' => $modules['admin_theme_posts'],
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
		 * Привязки постов к рубрикам
		 */
		'admin_linked_posts' => array(
			'layout'    => 'admin',
			'title'     => 'Привязки',
			'blocks'    => array(
				'content'   => array(
					'admin_linked_posts'=>$modules['admin_linked_posts'],
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
					'index_top_popular' => $modules['index_top_popular'],
					'index_top_new'     => $modules['index_top_new'],
					'index_rubrics'     => $modules['index_rubrics'],
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
		/**
		 * Страница поста
		 */
		'post_item'=> array(
			'layout'    => 'index',
			'title'     => 'Популярные записи. Самый быстрый ЖЖ Топ — Рейтинг записей Живого Журнала',
			'blocks'    => array(
				'header'   => array(
					'top_menu_index'=>$modules['top_menu_index'],
				),
				'content' => array(
					'post_item'=>$modules['post_item'],
				)
			)
		),
		/**
		 * Страница автора
		 */
		'author'=> array(
			'layout'    => 'index',
			'title'     => 'Популярные записи. Самый быстрый ЖЖ Топ — Рейтинг записей Живого Журнала',
			'blocks'    => array(
				'header'   => array(
					'top_menu_index'=>$modules['top_menu_index'],
				),
				'content' => array(
					'author'=>$modules['author'],
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