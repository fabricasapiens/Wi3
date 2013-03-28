<?php 

    echo "<form onsubmit='return false;'>";
        
        // Load the data that is associated with this field 
        $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();

        echo Wi3::inst()->formbuilder->textarea()->attr("name", "code")->html($data->code)->render();
		echo "<button onclick='wi3.request(\"pagefiller_default_component_livejavascript/edit\", {fieldid: \"" . $field->id . "\", code: $(this).closest(\"form\").find(\"[name=code]\").val()});'>code gebruiken</button>";
    
    echo "</form>";

 
?>
