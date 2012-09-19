<?php 

    echo "<form onsubmit='return false;'>";
	
		// TODO: nice looking 'menu' on the left-hand side to jump to edit-places. It should also indicate at which edit-place we are currently.
	
		// Build javascript code to fetch the correct values when user submits
		$jscode = "";	
		// Loop over every item of the model and create a corresponding input
		foreach($model as $name => $info) {
			if (isset($info["showoneditscreen"]) && $info["showoneditscreen"] === false) {

			} else {

				echo "<p><label for='" . $name . "' class='mediumpadding'>" . (isset($info->label) ? $info->label : $name) . "</label></p><p class='mediumpadding'>";
				// Fetch current value
				$currentvalue = $data->{$name};
				// Render input
				if ($info["type"] == "text") {
					if (isset($info["length"]) && (!is_numeric($info["length"]) || $info["length"] == 0 || $info["length"] > 50)) {
						$input = Wi3::inst()->formbuilder->textarea();
					} else {
						$input = Wi3::inst()->formbuilder->input();
					}
				} else if ($info["type"] == "date") {
					$input = Wi3::inst()->formbuilder->dateselector();
				} else if ($info["type"] == "image") {
					$input = Wi3::inst()->formbuilder->fileselector();
					if (isset($info["extensions"]) AND is_array($info["extensions"])) {
						$input->set("fileextensions", $info["extensions"]);
					}
				} else if ($info["type"] == "file") {
					$input = Wi3::inst()->formbuilder->fileselector();
					if (isset($info["extensions"]) AND is_array($info["extensions"])) {
						$input->set("fileextensions", $info["extensions"]);
					}
				} else if ($info["type"] == "folder") {
					$input = Wi3::inst()->formbuilder->folderselector();
					// Possibly only show folders that contain certain filetypes
					if (isset($info["extensions"]) AND is_array($info["extensions"])) {
						$input->set("fileextensions", $info["extensions"]);
					}
				} else if ($info["type"] == "number") {
					$input = Wi3::inst()->formbuilder->input();
				} else if ($info["type"] == "list") {
					// TODO: lists of elements with add/remove/move etc
					// TODO: some lists like tags have a special list-interface
					$input = Wi3::inst()->formbuilder->input();
				} else {
					die("the '" . $info["type"] . "' type of input is not supported");
				}
				$input->attr(Array("name" => $name, "class" => "fullwidth"));
				$input->val($currentvalue);
				echo $input->render();
				// Js code to fetch the element again when user submits
				$jscode .= ", " . $name . ": $(this).prevAll(\"form\").find(\"*[name=" . $name . "]\").val()";
				echo "</p>";
			}
		}
    
    echo "</form>";

    echo "<button onclick='wi3.request(\"pagefiller_default_component_" . $componentname . "/edit\", {fieldid: " . $field->id . $jscode . "});'>Opslaan</button>";

?>
