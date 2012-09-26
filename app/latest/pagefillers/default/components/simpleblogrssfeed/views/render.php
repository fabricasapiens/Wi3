<?php

	$link = new Wi3_Html_Link();
	echo $link->attr("href", $href)->attr("target", "_blank")->html("RSS")->render(); 

?>