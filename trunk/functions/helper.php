<?php
function gd_create_table_str($array, $col = 2)
{
	$count     = 0;
	$table_str = '<table>';
	
	foreach ($array as $key => $value) {
		// If we have a value for this item print it
		if ($value) {
			$title = esc_html($key);
			$val   = esc_html($value);
			
			if ($col == 2) {
				// 2 items per row
				if ($count % 2 == 0) {
					// Close the previous row if this isn't the first row
					if ($count != 0) {
						$table_str .= '</tr>';
					}
					$table_str .= '<tr>';
				}

				$table_str .= "<td><strong>{$title}:</strong> {$val}</td>";

				// If this was our last one close the row
				if ($count == count($array) - 1) {
					$table_str .= '</tr>';
				}
			} else {
				$table_str .= "<tr><td><strong>{$title}:</strong> {$val}</td></tr>";
			}

			$count++;
		}
	}
	
	$table_str .= '</table>';
	return $table_str;
}

function gd_state_list($current)
{
	$states = array(
		array('value' => '', 'label' => 'State'),
		array('value' => 'AL', 'label' => 'Alabama'),
		array('value' => 'AK', 'label' => 'Alaska'),
		array('value' => 'AZ', 'label' => 'Arizona'),
		array('value' => 'AR', 'label' => 'Arkansas'),
		array('value' => 'CA', 'label' => 'California'),
		array('value' => 'CO', 'label' => 'Colorado'),
		array('value' => 'CT', 'label' => 'Connecticut'),
		array('value' => 'DE', 'label' => 'Delaware'),
		array('value' => 'DC', 'label' => 'District Of Columbia'),
		array('value' => 'FL', 'label' => 'Florida'),
		array('value' => 'GA', 'label' => 'Georgia'),
		array('value' => 'HI', 'label' => 'Hawaii'),
		array('value' => 'ID', 'label' => 'Idaho'),
		array('value' => 'IL', 'label' => 'Illinois'),
		array('value' => 'IN', 'label' => 'Indiana'),
		array('value' => 'IA', 'label' => 'Iowa'),
		array('value' => 'KS', 'label' => 'Kansas'),
		array('value' => 'KY', 'label' => 'Kentucky'),
		array('value' => 'LA', 'label' => 'Louisiana'),
		array('value' => 'ME', 'label' => 'Maine'),
		array('value' => 'MD', 'label' => 'Maryland'),
		array('value' => 'MA', 'label' => 'Massachusetts'),
		array('value' => 'MI', 'label' => 'Michigan'),
		array('value' => 'MN', 'label' => 'Minnesota'),
		array('value' => 'MS', 'label' => 'Mississippi'),
		array('value' => 'MO', 'label' => 'Missouri'),
		array('value' => 'MT', 'label' => 'Montana'),
		array('value' => 'NE', 'label' => 'Nebraska'),
		array('value' => 'NV', 'label' => 'Nevada'),
		array('value' => 'NH', 'label' => 'New Hampshire'),
		array('value' => 'NJ', 'label' => 'New Jersey'),
		array('value' => 'NM', 'label' => 'New Mexico'),
		array('value' => 'NY', 'label' => 'New York'),
		array('value' => 'NC', 'label' => 'North Carolina'),
		array('value' => 'ND', 'label' => 'North Dakota'),
		array('value' => 'OH', 'label' => 'Ohio'),
		array('value' => 'OK', 'label' => 'Oklahoma'),
		array('value' => 'OR', 'label' => 'Oregon'),
		array('value' => 'PA', 'label' => 'Pennsylvania'),
		array('value' => 'RI', 'label' => 'Rhode Island'),
		array('value' => 'SC', 'label' => 'South Carolina'),
		array('value' => 'SD', 'label' => 'South Dakota'),
		array('value' => 'TN', 'label' => 'Tennessee'),
		array('value' => 'TX', 'label' => 'Texas'),
		array('value' => 'UT', 'label' => 'Utah'),
		array('value' => 'VT', 'label' => 'Vermont'),
		array('value' => 'VA', 'label' => 'Virginia'),
		array('value' => 'WA', 'label' => 'Washington'),
		array('value' => 'WV', 'label' => 'West Virginia'),
		array('value' => 'WI', 'label' => 'Wisconsin'),
		array('value' => 'WY', 'label' => 'Wyoming'),
	);
	
	foreach ($states as $state) {
		echo '<option value="' . $state['value'] . '" ' . selected($current, $state['value']) . '>' . $state['label']  . '</option>';
	}
}