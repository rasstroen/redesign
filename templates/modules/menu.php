<?php

function templateMenuListIndex($data)
{

	?>
	<div class="logo"><a href="/">Lj-top.ru</a></div>
	<ul class="menu index"><?php
	$menuItems = $data['items'];
	foreach($menuItems as $name => $item)
	{
		?><li <?php if(isset($item['selected'])) echo 'class="selected"'?>>
		<?php if(isset($item['items'])){?>
		<?=htmlspecialchars($item['title'])?>
		<ul class="subitem">
			<?php foreach($item['items'] as $subItem){?>
				<li <?php if(isset($subItem['selected'])) echo 'class="selected"'?>>
					<a href="<?=$subItem['url']?>">
						<?=htmlspecialchars($subItem['title'])?>
					</a>
				</li>
			<?php }?>
		</ul>
		<?php }else{?>
		<a href="<?=$item['url']?>">
			<?=htmlspecialchars($item['title'])?>
		</a>
		<?php }?>
		</li><?php
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