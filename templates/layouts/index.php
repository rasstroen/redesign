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
		<div class="header"><?=$view->renderBlock('header', $modulesData)?></div>
		<div class="wrapper">
			<div class="content"><?=$view->renderBlock('content', $modulesData)?></div>
		</div>
	</div>
	<div class="footer"><?=$view->renderBlock('footer', $modulesData)?></div>
</body>