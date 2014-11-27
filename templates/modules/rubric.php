<?php

function templateRubricListIndex($data)
{
	require_once 'post.php';
	?><h2>Рубрики</h2><?php
	foreach($data['rubrics'] as $rubricId => $rubric)
	{
		?><h3><a href="/rubric/<?=$rubric['name']?>"><?=$rubric['title']?></a></h3><?php
		foreach($rubric['posts'] as $postKey => $postLink)
		{
			$post = $data['posts'][$postKey];
			_drawTopPostInList($post);
		}
	}
}

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
		_drawRubricPostInList($post, $data['rubric']['rubric_id'], $data['isDone']);
	}
}

function _drawRubricPostInList($post, $rubricId, $isDone)
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

			<?php if(isset($post['phrases'])){ ?>
				<i>Совпадения по фразам:</i>
			<?php foreach($post['phrases'] as $phrase){?>
				фраза: <b><?=$phrase['phrase']?></b>
			<?php   }}?>
			<?php if(!$isDone) { ?>
			<form enctype="multipart/form-data" method="post">
				<input type="hidden" name="writemodule" value="admin/rubric">
				<input type="hidden" name="method" value="confirmLink">
				<input type="hidden" name="postId" value="<?=intval($post['post_id'])?>">
				<input type="hidden" name="authorId" value="<?=intval($post['author_id'])?>">
				<input type="hidden" name="pubDate" value="<?=intval($post['pub_time'])?>">
				<input type="hidden" name="rubricId" value="<?=$rubricId?>">
				<input type="submit" value="подтвердить привязку" />
			</form>
			<?php } else{?>
				<form enctype="multipart/form-data" method="post">
					<input type="hidden" name="writemodule" value="admin/rubric">
					<input type="hidden" name="method" value="deleteLink">
					<input type="hidden" name="postId" value="<?=intval($post['post_id'])?>">
					<input type="hidden" name="authorId" value="<?=intval($post['author_id'])?>">
					<input type="hidden" name="pubDate" value="<?=intval($post['pub_time'])?>">
					<input type="hidden" name="rubricId" value="<?=$rubricId?>">
					<input type="submit" value="удалить привязку" />
				</form>
			<?php }?>
		</div>
	</div>
<?php
}