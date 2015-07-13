<div id="gd-map-it-modal" class="modal">
	<div class="modal-backdrop fade in"></div>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close">
					<span aria-hidden="true">×</span>
				</button>
				<span>Map It</span>
			</div>
			<div class="modal-body">
				<iframe frameborder="0" src="<?=PROTOCOL?>maps.google.com/maps?oe=utf-8&amp;client=firefox-a&amp;q=<?=urlencode($listing->address->readable)?>&amp;output=embed"></iframe>
			</div>
		</div>
	</div>
</div>