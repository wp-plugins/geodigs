<?php
// Get the url without existing parameters
$this_url = strtok($_SERVER['REQUEST_URI'], '?');

// Pagination
if (isset($results->pagination)  && $results->pagination->totalPages->value > 1 && $hide_pagination == false) {
	$pagination       = $results->pagination;
	$current_listings = $pagination->currentListings;

	// Open
	echo '<div id="gd-pagination">';
		echo '<span>Page:</span>';

	// Previous Page
	if ($pagination->prevPage) {
		$url = $this_url . '?' . gd_edit_query_vars(array('listingsPage' => $pagination->prevPage->value));
		echo '<a id="gd-pagination-prev" href="' . $url . '">Previous</a>';
	}
	// Page numbers
	if ($pagination->currentPage && $pagination->totalPages && $pagination->totalPages->value > 1) {
		$current_page = $pagination->currentPage->value;
		$total_pages  = $pagination->totalPages->value;

		if ($current_page - 2 > 0) {
			for ($i = $current_page - 2; $i <= $current_page + 2 && $i <= $total_pages; $i++) {
				if ($i == $current_page) {
					echo '<span class="gd-pagination-current">' . $i . '</span>';
				}
				else {
					$url = $this_url . '?' . gd_edit_query_vars(array('listingsPage' => $i));
					echo '<a class="gd-pagination-page" href="' . $url . '">' . $i . '</a>';
				}
			}
		}
		else {
			for ($i = 1; $i <= 5 && $i <= $total_pages; $i++) {
				if ($i == $current_page) {
					echo '<span class="gd-pagination-current">' . $i . '</span>';
				}
				else {
					$url = $this_url . '?' . gd_edit_query_vars(array('listingsPage' => $i));
					echo '<a class="gd-pagination-page" href="' . $url . '">' . $i . '</a>';
				}
			}
		}

	}
	// Next Page
	if ($pagination->nextPage) {
		$url = $this_url . '?' . gd_edit_query_vars(array('listingsPage' => $pagination->nextPage->value));
		echo '<a id="gd-pagination-next" href="' . $url . '">Next</a>';
	}

	// Close
	echo '</div>';
}