<?php

$modules = array(
	/**
	 * Меню в шапке на главной
	 */
	'top_menu_index'             => array(
		'className' => '\Application\Module\Menu\Top',
		'template'  => 'menu',
		'action'    => 'list',
		'mode'      => 'index'
	),
	'top_banner'                 => array(
		'className' => '\Application\Module\Misc',
		'template'  => 'banner',
		'action'    => 'show',
		'mode'      => 'top'
	),
	'right-banner'               => array(
		'className' => '\Application\Module\Misc',
		'template'  => 'banner',
		'action'    => 'show',
		'mode'      => 'right'
	),
	'right-bottom-banner'        => array(
		'className' => '\Application\Module\Misc',
		'template'  => 'banner',
		'action'    => 'show',
		'mode'      => 'rightBottom'
	),
	'admin_themes'               => array(
		'className' => '\Application\Module\Admin\Theme',
		'template'  => 'theme',
		'action'    => 'list',
		'mode'      => 'admin'
	),
	'admin_theme_item'           => array(
		'className' => '\Application\Module\Admin\Theme',
		'template'  => 'theme',
		'action'    => 'show',
		'mode'      => 'item'
	),
	/**
	 * Меню в админке
	 */
	'admin_menu_index'           => array(
		'className' => '\Application\Module\Menu\Top',
		'template'  => 'menu',
		'action'    => 'list',
		'mode'      => 'admin'
	),
	/**
	 * Список рубрик в админке
	 */
	'admin_rubrics_list'         => array(
		'className' => '\Application\Module\Rubric',
		'template'  => 'admin',
		'action'    => 'list',
		'mode'      => 'adminRubrics'
	),
	/**
	 * Редактирование рубрики
	 */
	'admin_rubric_edit'          => array(
		'className' => '\Application\Module\Rubric',
		'template'  => 'admin',
		'action'    => 'edit',
		'mode'      => 'item'
	),
	/**
	 * Просмотр рубрики
	 */
	'admin_rubric'               => array(
		'className' => '\Application\Module\Rubric',
		'template'  => 'admin',
		'action'    => 'show',
		'mode'      => 'item'
	),
	/**
	 * Состояние демонов
	 */
	'admin_demons'               => array(
		'className' => '\Application\Module\Admin\Demons',
		'template'  => 'admin',
		'action'    => 'show',
		'mode'      => 'demons'
	),
	/**
	 * Топ авторов
	 */
	'authors_top'                => array(
		'className' => '\Application\Module\Author',
		'template'  => 'author',
		'action'    => 'list',
		'mode'      => 'top'
	),
	'index_top_authors'          => array(
		'className' => '\Application\Module\Author',
		'template'  => 'author',
		'action'    => 'list',
		'mode'      => 'indexTop',
		'variables' => array(
			'limit' => 5
		)
	),
	'index_top_communities'      => array(
		'className' => '\Application\Module\Author',
		'template'  => 'author',
		'action'    => 'list',
		'mode'      => 'indexTop',
		'variables' => array(
			'limit' => 5,
			'type'  => 1//\Application\BLL\Author::AUTHOR_TYPE_COMMUNITY
		)
	),
	/**
	 * топ популярных постов на главной
	 */
	'index_top_popular_top3'     => array(
		'className' => '\Application\Module\Post',
		'template'  => 'post',
		'action'    => 'list',
		'mode'      => 'indexTopPopular',
		'variables' => array(
			'limit' => 3
		)
	),
	'index_top_popular_popular9' => array(
		'className' => '\Application\Module\Post',
		'template'  => 'post',
		'action'    => 'list',
		'mode'      => 'indexTopPopular',
		'variables' => array(
			'offset' => 10,
			'limit'  => 9
		)
	),
	'index_top_popular_popular3' => array(
		'className' => '\Application\Module\Post',
		'template'  => 'post',
		'action'    => 'list',
		'mode'      => 'indexTopPopular',
		'variables' => array(
			'offset' => 3,
			'limit'  => 2
		)
	),
	'index_top_popular_readnow'  => array(
		'className' => '\Application\Module\Post',
		'template'  => 'post',
		'action'    => 'list',
		'mode'      => 'indexTopRead',
		'variables' => array(
			'offset' => 19,
			'limit'  => 4
		)
	),
	'index_top_commented'        => array(
		'className' => '\Application\Module\Post',
		'template'  => 'post',
		'action'    => 'list',
		'mode'      => 'indexTopCommented',
		'variables' => array(
			'offset' => 5,
			'limit'  => 5
		)
	),
	/**
	 * топ новых на главной
	 */
	'index_top_new'              => array(
		'className' => '\Application\Module\Post',
		'template'  => 'post',
		'action'    => 'list',
		'mode'      => 'indexTopNew',
		'variables' => array(
			'limit' => 5
		)
	),
	/**
	 * топ новых на главной
	 */
	'post_item'                  => array(
		'className' => '\Application\Module\Post',
		'template'  => 'post',
		'action'    => 'show',
		'mode'      => 'item'
	),
	/**
	 * Страница автора
	 */
	'author'                     => array(
		'className' => '\Application\Module\Author',
		'template'  => 'author',
		'action'    => 'show',
		'mode'      => 'item'
	),
	/**
	 * привязки постов
	 */
	'admin_linked_posts'         => array(
		'className' => '\Application\Module\Rubric',
		'template'  => 'rubric',
		'action'    => 'list',
		'mode'      => 'adminLinkedPosts'
	),
	/**
	 * рубрики на главной
	 */
	'index_rubrics'              => array(
		'className' => '\Application\Module\Rubric',
		'template'  => 'rubric',
		'action'    => 'list',
		'mode'      => 'index'
	),
	'admin_theme_posts'          => array(
		'className' => '\Application\Module\Admin\Theme',
		'template'  => 'theme',
		'action'    => 'list',
		'mode'      => 'adminThemePosts'
	),
	'index_top_themes'           => array(
		'className' => '\Application\Module\Theme',
		'template'  => 'theme',
		'action'    => 'list',
		'mode'      => 'mainSlider'
	),
	'index_top_video'           => array(
		'className' => '\Application\Module\Video',
		'template'  => 'video',
		'action'    => 'list',
		'mode'      => 'mainSlider'
	),
	'index_top_public'           => array(
		'className' => '\Application\Module\Publics',
		'template'  => 'public',
		'action'    => 'list',
		'mode'      => 'mainSlider'
	),
);

return array(
	'map'     => array(
		'top'    => array(

			'authors'   => array(
				'_var' => 'authorType',
				''     => 'authors_top',
			),
			'community' => array(
				'_var' => 'authorType',
				''     => 'authors_top',
			),
		),
		'author' => array(
			'%s' => array(
				'_var' => 'username',
				''     => 'author',
			)
		),
		'post'   => array(
			'%s' => array(
				'_var' => 'username',
				'%d'   => array(
					'_var' => 'postId',
					''     => 'post_item',
				)
			)
		),
		/**
		 * Главная страница
		 */
		''       => 'index',
		/**
		 * управление сайтом
		 */
		'admin'  => array(
			''       => 'admin',
			'rubric' => array(
				''   => 'admin_rubrics',
				'%d' => array(
					'_var'   => 'rubricId',
					''       => 'admin_rubric',
					'edit'   => 'admin_rubric_edit',
					'linked' => array(
						''        => 'admin_linked_posts',
						'done'    => array(
							''     => 'admin_linked_posts',
							'_var' => 'isDone',
						),
						'abandon' => array(
							''     => 'admin_linked_posts',
							'_var' => 'isDone',
						),
					)
				)
			),
			'demons' => array(
				'' => 'admin_demons'
			),
			'theme'  => array(
				''   => 'admin_themes',
				'%d' => array(
					'_var' => 'themeId',
					''     => 'admin_theme_item',
				)
			)
		),
	),
	'pages'   => array(
		/**
		 * Главная страница администрирования
		 */
		'admin'              => array(
			'layout' => 'admin',
			'title'  => 'Администрирование',
		),
		/**
		 * Раздел управлнеия рубриками
		 */
		'admin_rubrics'      => array(
			'layout' => 'admin',
			'title'  => 'Управление рубрикатором',
			'blocks' => array(
				'content' => array(
					'admin_rubrics_list' => $modules['admin_rubrics_list'],
				),
			)
		),
		/**
		 * Демоны
		 */
		'admin_demons'       => array(
			'layout' => 'admin',
			'title'  => 'Демоны',
			'blocks' => array(
				'content' => array(
					'admin_demons' => $modules['admin_demons'],
				),
			)
		),
		/**
		 * Темы
		 */
		'admin_themes'       => array(
			'layout' => 'admin',
			'title'  => 'Темы',
			'blocks' => array(
				'content' => array(
					'admin_themes' => $modules['admin_themes'],
				),
			)
		),
		/**
		 * Управление темой
		 */
		'admin_theme_item'   => array(
			'layout' => 'admin',
			'title'  => 'Тема',
			'blocks' => array(
				'content' => array(
					'admin_theme_item'  => $modules['admin_theme_item'],
					'admin_theme_posts' => $modules['admin_theme_posts'],
				),
			)
		),
		/**
		 * Редактирование рубрики
		 */
		'admin_rubric_edit'  => array(
			'layout' => 'admin',
			'title'  => 'Управление рубрикой',
			'blocks' => array(
				'content' => array(
					'admin_rubric_edit' => $modules['admin_rubric_edit'],
				),
			)
		),
		/**
		 * Просмотр рубрики в админке
		 */
		'admin_rubric'       => array(
			'layout' => 'admin',
			'title'  => 'Рубрика',
			'blocks' => array(
				'content' => array(
					'admin_rubric' => $modules['admin_rubric'],
				),
			)
		),
		/**
		 * Привязки постов к рубрикам
		 */
		'admin_linked_posts' => array(
			'layout' => 'admin',
			'title'  => 'Привязки',
			'blocks' => array(
				'content' => array(
					'admin_linked_posts' => $modules['admin_linked_posts'],
				),
			)
		),
		/**
		 * Главная страница
		 */
		'index'              => array(
			'layout' => 'index',
			'title'  => 'Популярные записи. Самый быстрый ЖЖ Топ — Рейтинг записей Живого Журнала',
			'blocks' => array(
				'header'                    => array(
					'top_banner' => $modules['top_banner'],
				),
				// block 1
				'block1-left-top'           => array(
					'index_top_themes' => $modules['index_top_themes'],
				),
				'block1-left-bottom'        => array(
					'index_top_popular_top3' => $modules['index_top_popular_top3'],
				),
				'block1-right-top'          => array(
					'index_top_popular_popular3' => $modules['index_top_popular_popular3'],
				),
				'block1-right-bottom-left'  => array(
					'index_top_authors' => $modules['index_top_authors'],
				),
				'block1-right-bottom-right' => array(
					'index_top_commented' => $modules['index_top_commented'],
				),
				// block 2
				'block2-left'               => array(
					'index_top_popular_popular9' => $modules['index_top_popular_popular9'],
				),
				'block2-right-left'         => array(
					'index_top_communities' => $modules['index_top_communities'],
				),
				'block2-right-right'        => array(
					'right-banner' => $modules['right-banner'],
				),
				'block2-right-bottom'       => array(
					'index_top_popular_readnow' => $modules['index_top_popular_readnow'],
				),
				// block 3
				'block3'                    => array(
					'index_top_new' => $modules['index_top_new'],
				),
				// block 4
				'block4-left'              => array(
					'index_top_video' => $modules['index_top_video'],
				),
				'block4-center'              => array(
					'index_top_public' => $modules['index_top_public'],
				),
				'block4-right'              => array(
					'right-bottom-banner' => $modules['right-bottom-banner'],
				),
			)
		),
		/**
		 * Топ авторов
		 */
		'authors_top'        => array(
			'layout' => 'inner',
			'title'  => 'Популярные записи. Самый быстрый ЖЖ Топ — Рейтинг записей Живого Журнала',
			'blocks' => array(
				'header'  => array(
					'top_menu_index' => $modules['top_menu_index'],
				),
				'content' => array(
					'authors_top' => $modules['authors_top'],
				)
			)
		),
		/**
		 * Страница поста
		 */
		'post_item'          => array(
			'layout' => 'inner',
			'title'  => 'Популярные записи. Самый быстрый ЖЖ Топ — Рейтинг записей Живого Журнала',
			'blocks' => array(
				'header'  => array(
					'top_menu_index' => $modules['top_menu_index'],
				),
				'content' => array(
					'post_item' => $modules['post_item'],
				)
			)
		),
		/**
		 * Страница автора
		 */
		'author'             => array(
			'layout' => 'inner',
			'title'  => 'Популярные записи. Самый быстрый ЖЖ Топ — Рейтинг записей Живого Журнала',
			'blocks' => array(
				'header'  => array(
					'top_menu_index' => $modules['top_menu_index'],
				),
				'content' => array(
					'author' => $modules['author'],
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
			'css'    => array(
				'reset' => '/static/css/reset.css',
				'admin' => '/static/css/layout/admin.css',
			),
			'blocks' => array(
				'header' => array(
					'admin_menu_index' => $modules['admin_menu_index'],
				),
			)
		),
		'index' => array(
			'css'    => array(
				'reset' => '/static/css/reset.css',
				'index' => '/static/css/layout/index.css',
				'posts' => '/static/css/posts.css',
			),
			'blocks' => array(
				'header' => array(
					'top_menu_index' => $modules['top_menu_index'],
				),
			)
		),
		'inner' => array(
			'css'    => array(
				'reset' => '/static/css/reset.css',
				'index' => '/static/css/layout/inner.css',
				'posts' => '/static/css/posts.css',
			),
			'blocks' => array(
				'header' => array(
					'top_menu_index' => $modules['top_menu_index'],
				),
			)
		)
	)
);