<?php
function templatePostListIndexTopPopular(array $data)
{
	?><h2>Популярное</h2><?php
	foreach($data['posts'] as $post)
	{
		_drawTopPostInList($post);
	}
}

function templatePostListIndexTopNew(array $data)
{
	?><h2>Свежие</h2><?php
	foreach($data['posts'] as $post)
	{
		_drawTopPostInList($post);
	}
}

function _drawTopPostInList($post)
{
	?>
	<div class="post_list_item main_popular">
		<span><a href="#"><?=htmlspecialchars($post['author']['username'])?></a></span>
		<span><a href="#"><?=htmlspecialchars($post['title'])?></a></span>
		<div class="text">
			<?=htmlspecialchars($post['short'])?>
			<br>
			<?=$post['comments']?>
			<br>
			<?=$post['pub_date']?>
			<br>
		</div>
	</div>
<?php

}