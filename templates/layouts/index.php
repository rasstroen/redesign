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
		<div class="content block1">
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
	</div>
</div>
<div class="footer"><?= $view->renderBlock('footer', $modulesData) ?></div>
</body>