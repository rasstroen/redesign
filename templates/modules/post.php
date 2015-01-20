<?php

function templatePostShowItem(array $data)
{
	$post = $data['post'];
	?>
	<div class="post_item clearfix">
		<div>
			<h3>
				<span><?= htmlspecialchars($post['author']['username']) ?></span>
				<span><?= htmlspecialchars($post['title']) ?></span>
			</h3>
		</div>
		<div class="text"><?= trim($post['text']) ?><br>
			<?= $post['comments'] ?>
			<br>
			<?= $post['pub_date'] ?>
			<br>
		</div>
	</div>
<?php
}

function templatePostListIndexTopRead(array $data)
{
	?><h2>Читают сейчас</h2><?php
	foreach($data['posts'] as $post)
	{
		_drawTopPostInList($post);
	}
}

function templatePostListIndexTopPopular(array $data)
{
	?><h2>Популярные</h2><?php
	foreach($data['posts'] as $post)
	{
		_drawTopPostInList($post);
	}
}

function templatePostListIndexTopCommented(array $data)
{
	?><h2>Комментируемые</h2><?php
	foreach($data['posts'] as $post)
	{
		?>
		<div class="post_list_item main_popular clearfix">
			<div>
				<h3>
					<span><a href="/author/<?= htmlspecialchars($post['author']['username']) ?>"><?= htmlspecialchars(
								$post['author']['username']
							) ?></a></span>
					<span><a href="/post/<?= htmlspecialchars($post['author']['username']) ?>/<?= htmlspecialchars(
							$post['post_id']
						) ?>"><?= htmlspecialchars($post['title']) ?></a></span>
				</h3>
			</div>

			<div class="text"><?= trim($post['short_5']) ?>
			</div>
		</div>
	<?php
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
	<div class="post_list_item main_popular clearfix">
		<div>
			<h3>
				<span><a href="/author/<?= htmlspecialchars($post['author']['username']) ?>"><?= htmlspecialchars(
							$post['author']['username']
						) ?></a></span>
				<span><a href="/post/<?= htmlspecialchars($post['author']['username']) ?>/<?= htmlspecialchars(
						$post['post_id']
					) ?>"><?= htmlspecialchars($post['title']) ?></a></span>
			</h3>
		</div>
		<?php if($post['has_pic'] == \Application\BLL\Posts::PIC_STATUS_HAS_PIC)
		{ ?>
			<div class="pic">
				<img src="<?= $post['image_src_normal'] ?>">
			</div>
		<?php }
		elseif($post['has_pic'] == \Application\BLL\Posts::PIC_STATUS_HAS_WIDE_PIC)
		{ ?>
			<div class="widepic">
				<img src="<?= $post['image_src_normal'] ?>">
			</div>
		<?php } ?>
		<div class="text"><?= trim($post['short']) ?><br>
			<?= $post['comments'] ?>
			<br>
			<?= $post['pub_date'] ?>
			<br>
		</div>
	</div>
<?php
}