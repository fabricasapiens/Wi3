<?php

	$this->css("style.css");

	if (!empty($username) && !empty($id)) {
		echo "<div class='component_githubgist'>";
			echo '<script src="https://gist.github.com/' . $username . '/' . $id . '.js"></script>';
		echo "</div>";
	} else {
		echo "Gist is not yet configured.";
	}

?>