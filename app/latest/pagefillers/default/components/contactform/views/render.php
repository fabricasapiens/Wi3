<?php

	$this->javascript("js.js");

	// Load wi3 plugin, so we can do wi3 requests
    Wi3::inst()->plugins->load("plugin_jquery_wi3"); // depends on JQuery Core, so no need to include it separately

	echo "<h2 style='width: 100%;'>Contactformulier</h2>";
	
	echo "<label for='name'>Naam</label>";
	echo "<input name='name' placeholder='naam' style='width: 100%;'/>";
	echo "<label for='emailaddress'>Emailadres</label>";
	echo "<input name='emailaddress' type='email' placeholder='emailadres' style='width: 100%;'/>";
	echo "<label for='subject'>Onderwerp</label>";
	echo "<input name='subject' placeholder='onderwerp' style='width: 100%;'/>";
	echo "<label for='message'>Bericht</label>";
	echo "<textarea name='message' placeholder='bericht' style='width: 100%; height: 300px;'/>";
	echo "<button onclick='wi3.pagefiller[\"default\"].component.contacform.submit(this);'>Verzenden</button>";

?>