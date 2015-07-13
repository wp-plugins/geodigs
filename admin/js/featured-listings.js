jQuery(document).ready(function($) {

/**
 * SECTION 1
 * Handles the sorting of the listings
 */
	var featuredListingsForm = $('#gd-featured-listings-settings'),
		featuredListingsTable = $('#gd-featured-listings-settings table.listings tbody');

	featuredListingsTable.sortable({
		update: function (event, ui) {
			// Store the order in an input field so php can grab it
			$('input[name="gd_featured_sort_order"]').attr('value', featuredListingsTable.sortable('toArray'));
		}
	});

/**
 * SECTION 2
 * Handles the adding of a listing
 */
//  	var newListingButton = $('#gd-add-new-listing-button'),
//  		newListingInput = $('#gd-new-listing');

//  	newListingButton.on('click', function() {
//  		// Tell the API to add a listing
// 		$.ajax({
// 			url: 'http://api.geodigs.com/v1/featured/' + newListingInput.val(),
// 			type: 'POST',
// 			success: function(response) {
				
// 			}
// 		});
//  	});
});