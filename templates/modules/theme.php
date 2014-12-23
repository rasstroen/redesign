<?php
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