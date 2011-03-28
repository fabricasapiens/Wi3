<?php 

    echo "<form onsubmit='return false;'>";
        
        // Load the data that is associated with this field 
        $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();

        echo Wi3::inst()->formbuilder->input()->attr("name", "url")->attr("value", $data->url)->render();

        // echo Wi3::inst()->formbuilder->fileselector()->attr("name", "image")->set("extensions", Array("jpg", "jpeg"))->addextension("png")->set("selected", $fileid)->render();
        // TODO: select currently selected image
    
    echo "</form>";

    echo "<button onclick='wi3.request(\"pagefiller_default_component_link/edit\", {fieldid: \"" . $field->id . "\", url: $(this).prevAll(\"form\").find(\"[name=url]\").val()});'>url gebruiken</button>";

?>
