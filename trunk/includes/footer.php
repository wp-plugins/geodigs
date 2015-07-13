<footer id="gd-footer">
	<div id="gd-disclaimers">
		<?php foreach ($_SESSION['gd_agent']->sources as $source): ?>
			<div class="gd-disclaimer">
				<?php if ($source->image): ?>
					<img src="<?=GD_URL_IMAGES . 'sources/' . $source->id . '-md.jpg'?>" alt="<?=$source->name?>" class="img-responsive" />
				<?php endif; ?>
				<?=$source->disclaimer?>
			</div>
		<?php endforeach; ?>
	</div>
</footer>