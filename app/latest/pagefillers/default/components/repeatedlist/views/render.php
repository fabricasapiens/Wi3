<?php

	if ($renderedinadminarea) {
		$this->javascript("edit.js");
		echo "<button onclick='wi3.pagefillers.default.components.repeatedlist.addAtTop(this);'>Voeg hier één toe</button> ";
	}

	// Get copy of raw inner HTML, so that it can be used to find innerFields
	$innerFieldHTML = pq($pqfield)->html();

	// Render a repeated list of fields
	foreach($elements as $index => $timeIndex) {

		// Find all fields in this html, and add a counter to their name
		$innerFieldInstance = phpQuery::newDocument($innerFieldHTML);
		$innerFields = pq($innerFieldInstance)->find("cms[type=field]");
		foreach($innerFields as $innerField) {
			$fieldname = pq($innerField)->attr("fieldname");
			$fieldname .= ("_" . $timeIndex);
			pq($innerField)->attr("fieldname", $fieldname);
		}
		echo pq($innerFieldInstance)->html();
		if ($renderedinadminarea) {
			echo "<button onclick='wi3.pagefillers.default.components.repeatedlist.addAfter(this, \"" . $timeIndex . "\");'>Voeg hier één toe</button> ";
			echo "<button onclick='wi3.pagefillers.default.components.repeatedlist.remove(this, \"" . $timeIndex . "\");'>Verwijder bovenstaande</button>";
		}
	}

?>