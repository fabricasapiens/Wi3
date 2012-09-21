<?php

	if (count($articles) == 0) {
		echo "Er zijn op dit moment geen artikelen.";
	} else {
		foreach($articles as $index => $article) {
			echo "<a href='" . $article->pageurl . "' style='display: block;'>";
				echo "<div>";
					echo "<span style='float: right;'>" . date("Y-m-d H:i", (int)$data->entertimestamp)  . 
						(!empty($data->edittimestamp) ? " (updated " . date("Y-m-d H:i", (int)$data->edittimestamp) . ")" : "") . 
					"</span>";
					echo "<h2>" . $article->title . "</h2>";
					echo "<img style='float: left; margin-bottom: 10px; margin-right: 10px;' src='" . $article->imageurl .  "'/>";
					echo "<div>" . $article->summary . "</div>";
					echo "<div style='clear:both; visibility: hidden; font-size: 1px;'>.</div>";
				echo "</div>";
			echo "</a>";
		}
	}

?>