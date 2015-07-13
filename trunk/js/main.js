var siteUrl = 'http://' + document.domain + '/';

(function($) {
	$(document).ready(function() {
		// Toggle favorite button
		$('.gd-favorite-toggle').click(function(e) {
			var url          = '/favorites/',
				listing_id   = $(this).attr('data-listing-id'),
				toggle_class = 'gd-favorite-toggle-on',
				toggle         = $(this);
			
			if (toggle.hasClass(toggle_class)) {
				url += 'delete/?listing_id=' + listing_id;
			}
			else {
				url += 'add/?listing_id=' + listing_id;
			}
			
			$.ajax({
				url: url,
				success: function(data, textStatus, jqXHR) {
					toggle.toggleClass(toggle_class);
				}
			});
		});
		
		// Back button  (for back to search results typically)
		$('#gd-back').click(function(e) {
			e.preventDefault();
			window.history.back();
		});
		
		// Search Results sort by
		$('.gd-sorting-control select').change(function(e) {
			var url              = window.location.href,
				val              = $(this).val(),
				sort             = val.split('::'),
				orderBy          = sort[0],
				orderByDirection = sort[1];
			
			url = updateQueryStringParameter(url, 'orderBy', orderBy);
			url = updateQueryStringParameter(url, 'orderByDirection', orderByDirection);
			window.location.assign(url);
		});
	});
	
	// Numbers only
	$('.number-input').keyup(function() {
		this.value = this.value.replace(/[^0-9\.]/g,'');
	});
	
	// Toggle accordian
	$('.gd-accordian .gd-accordian-title').click(function(e) {
		$(this).parent().toggleClass('gd-accordian-show');
	});
	
	// Modals
	$('.modal-open').click(function(e) {
		var modalId = $(this).attr('data-modal-id'),
			modal = $('#' + modalId);
		
		modal
			.addClass('modal-opened')
			.fadeIn(200);
		
		if (modalId == 'gd-quick-search-modal') {
			modal.find('.gd-quick-search-selector').focus();
		}
		
		if (modalId == 'gd-mortgage-calc-modal') {console.log($('#gd-mortgage-calc-taxes'));
			modal.find('#gd-mortgage-calculator-price').val($('#gd-mortgage-calc-price').val());
			modal.find('#gd-mortgage-calculator-annual-taxes').val($('#gd-mortgage-calc-taxes').val());
			dosum();
		}
		
		e.preventDefault();
	});
	
	$('.modal .close').click(function(e) {
		$('.modal-opened')
			.removeClass('modal-opened')
			.fadeOut(200);
	});
	
	// Ajax loading for lists
	$('.ajax-list').each(function() {
		var list = $(this);
		var url = list.attr('data-url');
		
		// Figure out if a relative or absolute file path was provided
		if (url.indexOf(siteUrl) == -1) {
			url = siteUrl + list.attr('data-url');
		}
		
		var items = $.getJSON(url, function(data) {
			var options = [];
			
			$.each(data, function(key, val) {
				var option = $('<option></option>')
					.text(val.readable)
					.val(val.id);
				
				if (list.attr('data-selected') != '') {
					if (list.attr('data-selected') == val.id) {
						option.attr('selected', 'selected');
					}
				}
				else if (list.attr('data-default') != '') {
					if (list.attr('data-default') == val.id) {
						option.attr('selected', 'selected');
					}
				}
				
				list.append(option);
			});
			list.trigger('ajax-list:updated');
		})
		.fail(function( jqxhr, textStatus, error ) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	});
	
})(jQuery);

function updateQueryStringParameter(uri, key, value) {
	var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	var separator = uri.indexOf('?') !== -1 ? "&" : "?";
	
	if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	}
	else {
		return uri + separator + key + "=" + value;
	}
}

// For easy string formatting
// First, checks if it isn't implemented yet.
if (!String.prototype.format) {
	String.prototype.format = function() {
		var args = arguments;
		return this.replace(/{(\d+)}/g, function(match, number) { 
			return typeof args[number] != 'undefined' ? args[number] : match;
		});
	};
}