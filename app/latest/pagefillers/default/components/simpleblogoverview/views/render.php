<?php

	if (count($articles) == 0) {
		echo "Er zijn op dit moment geen artikelen.";
	} else {
		foreach($articles as $index => $article) {
			echo "<a href='" . $article->pageurl . "' style='display: block; margin-bottom: 30px; color: inherit;'>";
				echo "<div>";
					echo "<h2 style='width: 100%;'>" . $article->title . "</h2>";
					echo "<div style='font-size: 0.75em; margin-bottom: 20px;'>" . date("Y-m-d", (int)$article->entertimestamp)  . 
						//(!empty($article->edittimestamp) ? " (updated " . date("Y-m-d H:i", (int)$article->edittimestamp) . ")" : "") . 
					"<span style='padding-left: 20px; padding-right: 20px;'>|</span>" .
					$article->keywords .
					"</div>";
					echo "<div style='text-align: justify;'>";
						echo "<img style='float: left; margin-right: 20px;' src='" . $article->imageurl .  "'/>";
						echo "<div>" . $article->summary . "</div>";
						echo "<div style='clear:both; visibility: hidden; font-size: 1px;'>.</div>";
					echo "</div>";
				echo "</div>";
			echo "</a>";
		}
	}

?>