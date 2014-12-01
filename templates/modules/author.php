<?php
function templateAuthorShowItem(array $data)
{
	$author = $data['author'];
	echo '<pre>';
	print_r($author);
	echo '</pre>';
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