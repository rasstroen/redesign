<?php

function templateThemeListAdminThemePosts($data)
{
	foreach($data['posts'] as $post)
	{
		_drawThemePostInList($post);
	}
}
function _drawThemePostInList($post)
{
	?>
	<div class="post_list_item main_popular clearfix" style="margin-bottom:20px">
		<div>
			<h3>
				<span><a href="/author/<?=htmlspecialchars($post['author']['username'])?>"><?=htmlspecialchars($post['author']['username'])?></a></span>
				<span><a href="/post/<?=htmlspecialchars($post['author']['username'])?>/<?=htmlspecialchars($post['post_id'])?>"><?=htmlspecialchars($post['title'])?></a></span>
			</h3>
		</div>
		<?php if($post['has_pic'] == \Application\BLL\Posts::PIC_STATUS_HAS_PIC){?>
			<div class="pic">
				<img src="<?=$post['image_src']?>">
			</div>
		<?php } elseif($post['has_pic'] == \Application\BLL\Posts::PIC_STATUS_HAS_WIDE_PIC) {?>
			<div class="widepic">
				<img src="<?=$post['image_src']?>">
			</div>
		<?php }?>
		<div class="text"><?=trim($post['short'])?><br>
			<?=$post['pub_date']?>
		</div>
	</div>
<?php
}
function templateThemeListAdmin(array $data)
{
	?><h1>Темы</h1>

	<form enctype="multipart/form-data" method="post">
		<input type="hidden" name="writemodule" value="admin/theme">
		<input type="hidden" name="method" value="recalc">
		<input type="submit" value="перепривязать все темы" />
	</form>

	<?php
	foreach($data['themes'] as $theme)
	{
		?>
		<div>
			<a href="<?= $theme['editUrl'] ?>">
				<?= htmlspecialchars($theme['title']) ?>
			</a>
		</div>
	<?php
	}
	?>
	<a href="<?= $data['addUrl'] ?>">Создать тему</a>
<?php
}

function templateThemeShowItem(array $data)
{
	if(isset($data['themes']))
	{
		$data['theme'] = reset($data['themes']);
	}
	if(isset($data['theme']))
	{
		?><h1>Тема "<?= $data['theme']['title'] ?>"</h1><?php
	}
	else
	{
		?><h1>Создание темы</h1><?php
	}

	?>

	<form enctype="multipart/form-data" method="post">
		<input type="hidden" name="writemodule" value="admin/theme">
		<input type="hidden" name="method" value="update">
		<input type="hidden" name="themeId" value="<?= $data['themeId'] ?>">

		<div>
			ник <input name="name" value="<?= isset($data['theme']['name']) ? $data['theme']['name'] : '' ?>">
		</div>
		<div>
			тайтл <input name="title" value="<?= isset($data['theme']['title']) ? $data['theme']['title'] : '' ?>">
		</div>
		<div>
			годна до YYYY-MM-DD <input name="finish" value="<?= isset($data['theme']['finish'])
				?
				$data['theme']['finish']
				:
				'' ?>">
		</div>
		<input type="submit" value="сохранить"/>
	</form>

	<?php if(isset($data['theme'])){?>
	<h1>Управление фразами</h1>
	<?php if(isset($data['theme']['phrases']))
	{
		foreach($data['theme']['phrases'] as $phrase)
		{
			?><div><?=htmlspecialchars($phrase)?>
			<a
			href="?writemodule=admin/theme&method=delete&themeId=<?=$data['theme']['theme_id']?>&phrase=<?=urlencode
		($phrase)
		?>">удалить</a></div><?php
		}
	}
		?>
	<h2>добавление фразы</h2>
	<form method="post">
		<input type="hidden" name="writemodule" value="admin/theme">
		<input type="hidden" name="method" value="add_phrase">
		<input type="hidden" name="themeId" value="<?= $data['theme']['theme_id'] ?>">
		<div>
			фраза <input name="phrase" value="">
		</div>
		<input type="submit" value="сохранить"/>
	</form>
	<?php }?>
<?php
}