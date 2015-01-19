<?php

function templateAuthorShowItem(array $data)
{

	$author = $data['author'];
	?>
	<div>
		<div class="pic">
			<?php if($author['journal_pic']){?>
				<img src="<?=$author['journal_pic'];?>">
			<?php }?>
		</div>
		<div class="naming">
			<h1><?=htmlspecialchars($author['username'])?></h1>
			<h2><?=htmlspecialchars($author['journal_title'])?></h2>
			<h3><?=htmlspecialchars($author['journal_subtitle'])?></h3>
		</div>
		<div>
			<?php if($author['position']) {?>
			<h3><?=$author['position']?> место в рейтинге</h3>
			<?php }?>
			<div>Журнал создан <?=date('d.m.Y', $author['journal_created'])?></div>
			<div>запостил <?=$author['journal_posted']?> записей, оставил <?=$author['journal_commented']?> комментариев, получил <?=$author['journal_comments_received']?> комментариев</div>
			<?php if($author['journal_country_code'] && $author['journal_city_name']){?>
				<div><?= $author['journal_city_name']?>, <?=$author['journal_country_code']?></div>
			<?php }?>
		</div>
	</div>

<?php
}


function templateAuthorListIndexTop(array $data)
{
	foreach($data['authors'] as $author)
	{

		?>
		<div>
			<span><img width="50px" src="<?=$author['journal_pic']?>"></span>
			<span>#<?=htmlspecialchars($author['position'])?></span>
			<span><h3><?=htmlspecialchars($author['username'])?></h3></span>
		</div>
	<?php
	}
}


function templateAuthorListTop(array $data)
{
	foreach($data['authors'] as $author)
	{

		?>
		<div>
			<span><img width="50px" src="<?=$author['journal_pic']?>"></span>
			<span>#<?=htmlspecialchars($author['position'])?></span>
			<span><h3><?=htmlspecialchars($author['username'])?></h3></span>
			<span><?=htmlspecialchars($author['rating_last_position'])?></span>
			<span><?=htmlspecialchars($author['journal_country_code'])?>, </span>
			<span><?=urldecode($author['journal_city_name'])?></span><br/>
			<span><?=urldecode($author['journal_title'])?></span><br/>
			<span><?=urldecode($author['journal_subtitle'])?></span>
			<hr>
		</div>
	<?php
	}
}