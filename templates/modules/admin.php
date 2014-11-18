<?php
function templateAdminShowItem(array $data)
{
	if($data['parentRubric']) {
		?><h1>Привязки записей к рубрике <a href="<?= $data['parentRubric']['adminUrl'] ?>">"<?= $data['parentRubric']['title'] ?>"</a> / "<?= $data['rubric']['title'] ?>"</h1><?php
	}else{
		?><h1>Привязки записей к рубрике "<?= $data['rubric']['title'] ?>"</h1><?php
	}
}
function templateAdminListAdminRubrics(array $data)
{
	?><h1>Управление рубриками</h1>
	<ul class="admin_rubrics">
	<div class="add"><a href="<?=$data['addUrl']?>">+добавить корневую рубрику</a></div>
	<?php
	$rubricsByParents = $data['rubricsByParents'];
	if(isset($rubricsByParents[0])) {
		foreach ($rubricsByParents[0] as $parentRubric) {
			?>
			<li><a class="item<?php if($parentRubric['deleted']) echo ' deleted';?>" href="<?=$parentRubric['adminUrl']?>"><?=htmlspecialchars($parentRubric['title'])?></a>
			<a href="<?=$parentRubric['addUrl']?>">+добавить подрубрику</a>
			<a href="<?=$parentRubric['editUrl']?>">редактировать</a>


			<?php if(!isset($rubricsByParents[$parentRubric['rubric_id']])){
				?>
				<a href="<?=$parentRubric['deleteUrl']?>">-<?=$parentRubric['deleted'] ? 'удалить' : 'скрыть'?></a>
				<?php if($parentRubric['deleted']) {?>
					<a href="<?=$parentRubric['restoreUrl']?>">показать</a>
					<?php }?>
			<?php } else {
				foreach ($rubricsByParents[$parentRubric['rubric_id']] as $childRubric) {
					?>
					<li>
					<a class="subitem item<?php if($childRubric['deleted']) echo ' deleted';?>"
					   href="<?= $childRubric['adminUrl'] ?>"><?= htmlspecialchars($childRubric['title']) ?></a>
					<a href="<?= $childRubric['editUrl'] ?>">редактировать</a>
					<a href="<?= $childRubric['deleteUrl'] ?>">-<?=$childRubric['deleted'] ? 'удалить' : 'скрыть'?></a>
					<?php if($childRubric['deleted']) {?>
						<a href="<?=$childRubric['restoreUrl']?>">показать</a>
					<?php }?>
					</li><?php
				}
			}
			?>
		</li>
		<?php
		}
	}
	?>

	<?php
	?></ul><?php
}

function templateAdminEditItem(array $data)
{
	if(isset($data['rubric'])) {
		?><h2>Редактирование рубрики "<?= $data['rubric']['title']?>"</h2><?php
	}
	else{
		?><h2>Добавление подрубрики <?php if(isset($data['parentRubric'])) echo ' рубрики "' . htmlspecialchars($data['parentRubric']['title']) . '"';?></h2><?php
	}
	?>
	<form enctype="multipart/form-data" method="post">
		<input type="hidden" name="writemodule" value="admin/rubric">
		<input type="hidden" name="method" value="edit">
		<input type="hidden" name="rubricId" value="<?=isset($data['rubric'])?$data['rubric']['rubric_id'] : 0?>">
		<input type="hidden" name="parentId" value="<?=isset($data['parentId'])?$data['parentId'] : 0?>">
		<div>
			<span>Заголовок</span>
			<input name="title" value="<?=isset($data['rubric']['title']) ? htmlspecialchars($data['rubric']['title']) : ''?>">
		</div>
		<div>
			<span>Имя для адресной строки</span>
			<input name="name" value="<?=isset($data['rubric']['name']) ? htmlspecialchars($data['rubric']['name']) : ''?>">
		</div>
		<input type="submit" value="Сохранить" />
	</form>
<?php
}