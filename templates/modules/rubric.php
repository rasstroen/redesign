<?php
function templateRubricListAdminLinkedPosts(array $data)
{
	?>
	<form enctype="multipart/form-data" method="post">
		<input type="hidden" name="writemodule" value="admin/rubric">
		<input type="hidden" name="method" value="recalc">
		<input type="submit" value="перегенерить автопривязки" />
	</form>
	<div class="choose"><?php
	if($data['isDone'])
	{
		?>
		<a href="<?=$data['notDoneUrl']?>">Непривязанные</a>
		<h1>Привязанные к рубрике "<?=htmlspecialchars($data['rubric']['title']);?>" активные посты</h1>
		<?php
	}
	else
	{
		?>
		<a href="<?=$data['doneUrl']?>">Привязанные</a>
		<h1>Не привязанные к рубрике "<?=htmlspecialchars($data['rubric']['title']);?>" активные посты</h1>
	<?php
	}
	?></div><?php
	foreach($data['posts'] as $post)
	{
		_drawRubricPostInList($post);
	}
}

function _drawRubricPostInList($post)
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
			<br/>
			<i>Совпадения по фразам:</i>
			<?php foreach($post['phrases'] as $phrase){?>
				фраза: <b><?=$phrase['phrase']?></b>
			<?php   }?>
		</div>
	</div>
<?php
}