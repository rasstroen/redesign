<?php

function templateAdminListAdminRubrics(array $data)
{
	?><h1>Управление рубриками</h1><ul class="admin_rubrics"><?php
	$rubricsByParents = $data['rubricsByParents'];
	if(isset($rubricsByParents[0])) {
		foreach ($rubricsByParents[0] as $parentRubric) {
			?>
			<li><a class="item" href="<?=$parentRubric['adminUrl']?>"><?=htmlspecialchars($parentRubric['title'])?></a>
			<?php if(isset($rubricsByParents[$parentRubric['rubric_id']])){
				foreach ($rubricsByParents[$parentRubric['rubric_id']] as $childRubric) {
					?><li>
						<a class="subitem item" href="<?=$childRubric['adminUrl']?>"><?=htmlspecialchars($childRubric['title'])?></a>
						<a href="<?=$childRubric['editUrl']?>">редактировать</a>
					</li><?php
				}

			}?>
			<div class="add"><a href="<?=$parentRubric['addUrl']?>">Добавить подрубрику</a></div>
		</li>
		<?php
		}
	}
	?>
		<div class="add"><a href="<?=$data['addUrl']?>">Добавить корневую рубрику</a></div>
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