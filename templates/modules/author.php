<?php
function templateAuthorListTop(array $data)
{
	foreach($data['authors'] as $author)
	{
		?>
		<div>
			<span><?=htmlspecialchars($author['position'])?></span>
			<span><?=htmlspecialchars($author['username'])?></span>
			<span><?=htmlspecialchars($author['rating_last_position'])?></span>
		</div>
	<?php
	}
}