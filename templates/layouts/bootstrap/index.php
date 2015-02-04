<?php
/**
 * @var array                           $modulesData
 * @var \Application\Web                $application
 * @var \Application\Component\View\Web $view
 */
require_once $application->configuration->getRootPath() . 'templates/helpers/view.php';
require_once $application->configuration->getRootPath() . 'templates/helpers/head.php';?>
<body>
<div class="container">
	<div class="row">
		<span class="col-md-60">
			<!-- верхний баннер--><?= $view->renderBlock('top-banner', $modulesData) ?>
		</span>
	</div>
	<div class="row">
		<span class="col-md-36 min-700">
			<!--верхнее меню--><?= $view->renderBlock('top-menu', $modulesData) ?>
		</span>
		<span class="col-md-24 search">
			<!--верхнее меню--><?= $view->renderBlock('search-menu', $modulesData) ?>
		</span>
	</div>
	<div class="row">
		<span class="col-md-36">
			<div ><!--слайдер с темами--><?= $view->renderBlock('index-theme-slider',
			                                                                           $modulesData) ?></div>
			<div><!-- 2 колонки популярного под сладйром тем--><?=
				$view->renderBlock('index-popular-columns-top',
			                                                                    $modulesData) ?></div>
		</span>
		<span class="col-md-24">
			<div>
				<span style="float: left; width: 240px;s margin-right: 20px">
					<div ><!--актуальное--><?= $view->renderBlock('index-actual',
					                                                                     $modulesData) ?></div>
				</span>
				<span style="float: left; width: 200px;">
					<div ><!--обсуждаемое--><?= $view->renderBlock('index-commented',
					                                                                      $modulesData) ?></div>
				</span>
			</div>
		</span>
	</div>
	<div class="row">
		<span class="col-md-36">
			<div style="margin-top:10px;"><!-- 2 колонки популярного ниже--><?= $view->renderBlock
				('index-popular-columns-bottom',
			                                                                    $modulesData) ?></div>
		</span>

		<span class="col-md-24">
			<div class="clearfix">
				<span style="float: left; width: 240px; margin-right: 20px">
					<div ><!--топ-5 авторов--><?= $view->renderBlock('index-top-authors',
					                                                                        $modulesData) ?></div>
				</span>
				<span style="float: left; width: 200px;">
					<div ><!--топ-5 сообществ--><?= $view->renderBlock
						('index-top-communities', $modulesData) ?></div>
				</span>
			</div>
			<div><!--Читают сейчас--><?= $view->renderBlock
				('index-readnow', $modulesData) ?></div>
		</span>
	</div>
	<div class="row" style="margin-top: 10px">
		<span class="col-md-60">
			<div ><!--Свежее из Живого Журнала--><?= $view->renderBlock
				('index-fresh-header', $modulesData) ?></div>
		</span>
	</div>
	<div class="row" style="margin-top: 10px"><!--Свежее из Живого Журнала--><?= $view->renderBlock
		('index-fresh', $modulesData) ?>
	</div>

	<div class="row" style="margin-top: 10px">
		<span class="col-md-36">
			<div ><!--слайдер с видео--><?= $view->renderBlock
				('index-slider-video', $modulesData) ?></div>
		</span>
		<span class="col-md-24">
			<div ><!--слайдер с пабликами--><?= $view->renderBlock
				('index-slider-piblics', $modulesData) ?></div>
		</span>
	</div>

	<div class="row">
		<span class="col-md-60">
			<div ><!--Футер--><?= $view->renderBlock
				('index-footer', $modulesData) ?></div>
		</span>
	</div>
</div>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/static/css/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>