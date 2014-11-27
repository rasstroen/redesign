<?php
function templateAdminShowItem(array $data)
{
	if($data['parentRubric']) {
		?><h1>"<a href="<?= $data['parentRubric']['adminUrl'] ?>"><?= $data['parentRubric']['title'] ?>"</a> / "<?= $data['rubric']['title'] ?>"</h1><?php
	}else{
		?><h1><?= $data['rubric']['title'] ?></h1><?php
	}
}

function templateAdminShowDemons(array $data)
{
	if(isset($data['worker']))
	{
		echo '<pre style="width: 100%; overflow: auto">';
		print_r($data['worker']);
		$tasks = unserialize($data['worker']['tasks']);
		if($tasks)
		foreach($tasks as $task)
		{
			echo date('Y-m-d H:i:s',$task['run_time']) . "\n";
			print_r(unserialize($task['data']));
		}
		echo '</pre>';
		return;
	}
	?><table colspan="1" border="1" width="100%">
	<tr>
		<td>Очередь</td>
		<td>Приоритет</td>
		<td>Задач в воркере</td>
		<td>Команда</td>
		<td>Включен</td>
		<td>Воркеров создано</td>
		<td>Воркеров свободно для создания</td>
		<td>Воркеров в работе</td>
	</tr>
	<?php
	foreach($data['queues'] as $queue)
	{
		$activeWorkersCnt = 0;
		foreach($queue['workers'] as $worker)
		{
			if($worker['pid'])
			{
				$activeWorkersCnt++;
			}
		}
		?>
		<tr>
			<td><?=htmlspecialchars($queue['name']);?></td>
			<td><?=htmlspecialchars($queue['priority']);?></td>
			<td><?=htmlspecialchars($queue['tasks_per_worker']);?></td>
			<td><?=htmlspecialchars($queue['command'] . '::' . $queue['method']);?></td>
			<td><?=htmlspecialchars($queue['enabled']);?></td>
			<td><?=htmlspecialchars($queue['status']['current_workers_count']);?></td>
			<td><?=htmlspecialchars($queue['status']['free_workers']);?></td>
			<td><?=htmlspecialchars($activeWorkersCnt);?></td>
		</tr>
		<?php if(count($queue['workers'])){?>
		<tr>
			<td colspan="8">
				<table width="100%">
					<tr>
						<td colspan="5">Id</td>
						<td>Задач</td>
						<td>Дата создания</td>
						<td>Pid</td>
					</tr>
					<?php foreach($queue['workers'] as $worker){
						$tasks = unserialize($worker['tasks']);
						?>
						<tr <?php if($worker['pid']) echo 'class="worker_with_pid"'?>>
							<td colspan="5"><a href="?workerId=<?=(int)$worker['worker_id'];?>"><?=htmlspecialchars($worker['worker_id']);?></a></td>
							<td><?=htmlspecialchars(count($tasks));?></td>
							<td><?=htmlspecialchars($worker['create_time']);?></td>
							<td><?=htmlspecialchars($worker['pid']);?></td>
						</tr>
					<?php }?>
				</table>
			</td>
		</tr>
		<?php }?>
	<?php
	}
	?></table><?php
}

function templateAdminListAdminRubrics(array $data)
{
	?><h1>Управление рубриками</h1>
	<form enctype="multipart/form-data" method="post">
		<input type="hidden" name="writemodule" value="admin/rubric">
		<input type="hidden" name="method" value="recalc">
		<input type="submit" value="перегенерить автопривязки" />
	</form>
	<ul class="admin_rubrics">
	<div class="add"><a href="<?=$data['addUrl']?>">+добавить корневую рубрику</a></div>
	<?php
	$rubricsByParents = $data['rubricsByParents'];
	if(isset($rubricsByParents[0])) {
		foreach ($rubricsByParents[0] as $parentRubric) {
			?>
			<li><a class="item<?php if($parentRubric['deleted']) echo ' deleted';?>" href="<?=$parentRubric['linkedUrl']?>">
				<?=htmlspecialchars($parentRubric['title'])?>
				(<?=intval($parentRubric['posts_count'])?>)
			</a>
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
					   href="<?= $childRubric['linkedUrl'] ?>">
						<?= htmlspecialchars($childRubric['title']) ?>
						(<?=intval($childRubric['posts_count'])?>)
					</a>
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

	<h3><a href="<?=$data['rubric']['linkedUrl']?>">Автопривязки</a></h3>
	<?php foreach($data['phrases'] as $phrase){?>
	<div>
		<form enctype="multipart/form-data" method="post">
			<input type="hidden" name="writemodule" value="admin/rubric">
			<input type="hidden" name="method" value="delWord">
			<input type="hidden" name="phraseId" value="<?=$phrase['phrase_id']?>">
		"<?=$phrase['phrase']?>", постов : <?=$phrase['posts_count']?>
			<input type="submit" value="удалить" />
		</form>
	</div>
	<?php }?>
	<h3>Добавить фразу</h3>
	<form enctype="multipart/form-data" method="post">
		<input type="hidden" name="writemodule" value="admin/rubric">
		<input type="hidden" name="method" value="addWord">
		<input type="hidden" name="rubricId" value="<?=isset($data['rubric'])?$data['rubric']['rubric_id'] : 0?>">
		<input type="hidden" name="parentId" value="<?=isset($data['parentId'])?$data['parentId'] : 0?>">
		<div>
			<span>Новая фраза</span>
			<input name="name" value="">
		</div>
		<input type="submit" value="добавить" />
	</form>

<?php
}