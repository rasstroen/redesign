<?php

function templateMenuListIndex($data)
{
	?><ul class="adminMenu"><?php
	$menuItems = $data['items'];
	foreach($menuItems as $name => $item)
	{
		?><li <?php if(isset($item['selected'])) echo 'class="selected"'?>><a href="<?=$item['url']?>"><?=htmlspecialchars($item['title'])?></a></li><?php
	}
	?></ul><?php
}

function templateMenuListAdmin($data)
{
	?><ul class="adminMenu"><?php
	$menuItems = $data['items'];
	foreach($menuItems as $name => $item)
	{
		?><li <?php if(isset($item['selected'])) echo 'class="selected"'?>><a href="<?=$item['url']?>"><?=htmlspecialchars($item['title'])?></a></li><?php
	}
	?></ul><?php
}