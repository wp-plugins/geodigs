(function($){
	$(document).ready(function(){

		// $items = [];
		var autocompleteCache;

		$( ".gd-quick-search-selector" ).autocomplete({
			minLength: 2,
			delay: 0,
			autoFocus: true,
			open: function(event, ui) {
				$('.ui-autocomplete').css('max-width', $(this).outerWidth());
			},
			response: function(event, ui) {
				$('.gd-quick-search-throbber').hide();
				$('.quick-search input[type="submit"]').prop("disabled", false);
			},
			search: function(event, ui) {
				$('.gd-quick-search-throbber').css("display", "inline-block");
				$('.quick-search input[type="submit"]').prop("disabled", true);
			},
			select: function(event, ui) {
				fillInFields(ui.item.fields);
			},
			source: function(request, response) {
				$.ajax({
					url:"http://api.geodigs.com/v1/listings/autocomplete",
					dataType: 'json',
					data: {
						search: request.term,
						sources: $('#gd-quick-search-sources').val().split(',')
					},
					success: function(data){
						var myResults = [];
						var index = 0;
						console.log(data);
						response($.map(data, function (item) {

							$.each(item, function(key, val)
							{
								myResults.push({
									label: val.readable,
									value: val.readable,
									fields: val.params,
									index: index
								});

								index++;
							});

							if(myResults == autocompleteCache)
								return null;
							else
							{
								autocompleteCache = myResults;
								return myResults;
							}
						}));
					},
					error: function(xhr, textStatus, errorThrown) {
						console.log(errorThrown);
					}
				});
			}
		});

		function fillInFields(fields) {
			$('.gd-quick-search-fields').empty();
			$.each(fields, function(key, value) {

				$('.gd-quick-search-fields').append('<input type="hidden" name="' + key + '" value="' + value + '"/>');

			});

			//$('#gd-quick-search-form').submit();
		}
	});
}(jQuery));