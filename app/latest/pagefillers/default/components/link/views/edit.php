<?php 

    echo "<form onsubmit='return false;'>";
        
        // Load the data that is associated with this field 
        $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();

        echo Wi3::inst()->formbuilder->input()->attr("name", "url")->attr("value", $data->url)->render();
		
		echo "<button onclick='wi3.request(\"pagefiller_default_component_link/edit\", {fieldid: \"" . $field->id . "\", url: $(this).find(\"[name=url]\").val()});'>url gebruiken</button>";
		echo "<p>Of klik op een van onderstaande bestanden om een link naar dat bestand te maken.</p>";
		echo "<div onClick='wi3.request(\"pagefiller_default_component_link/edit\", {fieldid: \"" . $field->id . "\", fileid: $(this).find(\"[name=file]\").val()});'>";
			if (!isset($data->fileid)) {
				$data->fileid = false;
			}
			echo Wi3::inst()->formbuilder->fileselector()->attr("name", "file")->set("selected", $data->fileid)->render();
		echo "</div>";
    
    echo "</form>";

 
?>
