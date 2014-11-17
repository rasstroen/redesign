<?php
/**
 * @var array $modulesData
 * @var \Application\Web $application
 * @var \Application\Component\View\Web $view
 */

?><!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title></title>
	<link rel="shortcut icon" href="/static/favicon.png">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<!--css-->
	<?php
	foreach($view->getCss() as $css)
	?><link rel="stylesheet" href="<?=$css?>"><?php
	?>
	<!--js-->

</head>