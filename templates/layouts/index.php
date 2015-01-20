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
	<div class="header"><?= $view->renderBlock('header', $modulesData) ?></div>
	<div class="wrapper">
		<!-- block 1 -->
		<div class="content_block content block1 clearfix">
			<div class="left">
				<div class="top">
					<?= $view->renderBlock('block1-left-top', $modulesData) ?>
				</div>
				<div class="bottom">
					<?= $view->renderBlock('block1-left-bottom', $modulesData) ?>
				</div>
			</div>
			<div class="right">
				<div class="top clearfix">
					<?= $view->renderBlock('block1-right-top', $modulesData) ?>
				</div>
				<div class="bottom clearfix">
					<div class="left">
						<?= $view->renderBlock('block1-right-bottom-left', $modulesData) ?>
					</div>
					<div class="right">
						<?= $view->renderBlock('block1-right-bottom-right', $modulesData) ?>
					</div>
				</div>
			</div>
		</div>
		<!-- block 2 -->
		<div class="content_block content block2 clearfix">
			<div class="left clearfix">
				<div class="top">
					<?= $view->renderBlock('block2-left', $modulesData) ?>
				</div>
			</div>
			<div class="right clearfix">
				<div class="left clearfix">
					<?= $view->renderBlock('block2-right-left', $modulesData) ?>
				</div>
				<div class="right clearfix">
					<?= $view->renderBlock('block2-right-right', $modulesData) ?>
				</div>
				<div class="bottom clearfix">
					<?= $view->renderBlock('block2-right-bottom', $modulesData) ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="footer"><?= $view->renderBlock('footer', $modulesData) ?></div>
</body>