<?php

	echo "<h2 style='width: 100%;'>" . $data->title . "</h2>";
	echo "<div style='font-size: 0.75em; margin-bottom: 20px;'>" . date("Y-m-d", (int)$data->entertimestamp)  . 
		(!empty($article->edittimestamp) ? " (updated " . date("Y-m-d H:i", (int)$article->edittimestamp) . ")" : "") . 
	"<span style='padding-left: 20px; padding-right: 20px;'>|</span>" .
	"keywords: " . $data->keywords .
	"</div>";

	echo "<img style='float: left; margin-bottom: 20px; margin-right: 20px;' src='" . $imageurl .  "'/>";	
	// Todo: we should be able to specify a callback on save so that we can store the data ourselves, if we wanted to.
	// Or at least provide the location where the data should be saved
	echo "<cms type='editableblock' name='blogtext'>Blogtext</cms>";

	echo "<div style='visibility:hidden; clear:both; font-size: 1px;'>.</div>";

?>