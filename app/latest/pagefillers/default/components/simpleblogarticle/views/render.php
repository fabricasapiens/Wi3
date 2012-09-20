<?php

	echo "<img style='float: left; margin-bottom: 20px; margin-right: 20px;' src='" . $imageurl .  "'/>";
	echo "<h2>" . $data->title . "</h2>";
	echo "<p>" . date("Y:m:d", (int)$data->edittimestamp) . "</p>";
	echo "<p>Keywords: " . $data->keywords . "</p>";
	
	// Todo: we should be able to specify a callback on save so that we can store the data ourselves, if we wanted to.
	// Or at least provide the location where the data should be saved
	echo "<cms type='editableblock' name='blogtext'>Blogtext</cms>";

	echo "<div style='visibility:hidden; clear:both; font-size: 1px;'>.</div>";

?>