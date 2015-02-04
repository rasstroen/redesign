<?php
function templateMenuListFooter()
{

}

function templateMenuListIndexSearch()
{
	?>
	<div class="search-menu">

		<ul class="search">
			<li class="form">
				<form>
					<input value="Поиск по записям" /> <div class="icon"></div><a>Вход</a>
				</form>
			</li>
		</ul>
	</div>
<?php
}

function templateMenuListIndex($data)
{

	?>
	<div class="top-menu">
	<ul class="menu index clearfix">
		<li class="logo"><a href="/">Lj-top.ru</a></li>
		<?php
	$menuItems = $data['items'];
	foreach($menuItems as $name => $item)
	{
		?><li <?php if(isset($item['selected'])) echo 'class="selected"'?>>
		<?php if(isset($item['items'])){?>
		<a><?=htmlspecialchars($item['title'])?></a>
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
		</li>
	<?php
	}
	?></ul><ul class="menu rubrics">
		<?php foreach($data['rubrics'] as $rubric){?>
			<li>
				<a href="<?=$rubric['url']?>"><?=htmlspecialchars($rubric['title'])?></a>
			</li>
	<?php }?>
		<li>
			<a class="more" >ещё</a>
		</li>
	</ul>
	</div>
<?php


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