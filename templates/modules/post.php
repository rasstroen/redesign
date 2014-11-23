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
		<?php if($post['has_pic'] == \Application\BLL\Posts::PIC_STATUS_HAS_PIC){?>
			<div class="widepic">
				<img src="<?=$post['image_src']?>">
			</div>
		<?php } elseif($post['has_pic'] == \Application\BLL\Posts::PIC_STATUS_HAS_WIDE_PIC) {?>
			<div class="pic">
				<img src="<?=$post['image_src']?>">
			</div>
		<?php }?>
		<div class="text"><?=trim($post['short'])?><br>
			<?=$post['comments']?>
			<br>
			<?=$post['pub_date']?>
			<br>
		</div>
	</div>
<?php
}