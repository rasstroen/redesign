<?php
function th_conditions_paging($data) {
	?>
	<div class="conditions_paging">
		<?php
		foreach ($data['pages'] as $index => $page) {
			?><div<?php echo $data['page'] == $index ? ' class="current"' : ''; ?>><a href="<?php echo $page['url'] ?>"><?php echo $page['title']; ?></a></div> <?php
		}
		?>
	</div>
<?php
}