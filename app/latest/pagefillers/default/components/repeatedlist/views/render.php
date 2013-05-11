<?php

	if ($renderedinadminarea) {

		$this->javascript("edit.js");

		echo "<button onclick='wi3.pagefillers.default.components.repeatedlist.increaseAmount(this);'>Voeg één toe na laatste</button> ";
		echo "<button onclick='wi3.pagefillers.default.components.repeatedlist.decreaseAmount(this);'>Verwijder laatste</button>";
	}

	// Render a repeated list of fields
	foreach($elements as $index => $element) {
		// Find all fields in this html, and add a counter to their name
		$innerFields = pq($pqfield)->find("cms[type=field]");
		foreach($innerFields as $innerField) {
			$fieldname = pq($innerField)->attr("fieldname");
			$fieldname .= ("_" . $index);
			pq($innerField)->attr("fieldname", $fieldname);
		}
		echo pq($pqfield)->html();
	}

	if ($renderedinadminarea) {
		echo "<button onclick='wi3.pagefillers.default.components.repeatedlist.increaseAmount(this);'>Voeg één toe na laatste</button> ";
		echo "<button onclick='wi3.pagefillers.default.components.repeatedlist.decreaseAmount(this);'>Verwijder laatste</button>";
	}

?>