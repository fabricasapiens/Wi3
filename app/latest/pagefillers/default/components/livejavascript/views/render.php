<?php

	// Dependencies
	Wi3::inst()->plugins->load("plugin_betterexamples");

	// Javascript
	$this->javascript("script.js");

	// CSS
	$this->css("style.css");

	// Create code containers
	echo "<div class='livejavascript_container'>";
		echo "<pre class='livejavascript_input' id='livejavascript_input_" . $fieldid . "'>" . $code . "</pre>";
		echo "<div class='livejavascript_output'></div>";
	echo "</div>";

?>