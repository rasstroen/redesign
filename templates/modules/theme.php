<?php
function templateThemeListAdmin(array $data)
{
	?><h1>Темы</h1><?php
	?>
<a href="<?=$data['addUrl']?>">Создать тему</a>
<?php
}

function templateThemeShowItem(array $data)
{
	?>
	<form enctype="multipart/form-data" method="post">
		<input type="hidden" name="writemodule" value="admin/theme">
		<input type="hidden" name="method" value="update">
		<input type="hidden" name="themeId" value="<?=$data['themeId']?>">
		<div>
			ник <input name="name" value="<?=isset($data['theme']['name']) ? $data['theme']['name'] : ''?>">
		</div>
		<div>
			тайтл <input name="title" value="<?=isset($data['theme']['title']) ? $data['theme']['title'] : ''?>">
		</div>
		<div>
			годна до YYYY-MM-DD <input name="finish" value="<?=isset($data['theme']['finish']) ?
				$data['theme']['finish'] :
				''?>">
		</div>
		<input type="submit" value="сохранить" />
	</form>
<?php
}