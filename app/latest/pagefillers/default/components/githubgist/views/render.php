<?php

	if (!empty($username) && !empty($id)) {
		echo '<script src="https://gist.github.com/' . $username . '/' . $id . '.js"></script>';
	} else {
		echo "Gist is not yet configured.";
	}

?>