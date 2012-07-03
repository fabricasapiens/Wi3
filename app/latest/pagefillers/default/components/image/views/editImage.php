<?php 

    echo "<form onsubmit='return false;'>";
        
        $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("image")->load();
        $fileid = $imagedata->data;
        echo Wi3::inst()->formbuilder->fileselector()->attr("name", "image")->set("extensions", Array("jpg", "jpeg"))->addextension("png")->set("selected", $fileid)->render();
        // TODO: select currently selected image
    
    echo "</form>";

    echo "<button onclick='wi3.request(\"pagefiller_default_component_image/editImage\", {fieldid: " . $field->id . ", image: $(this).prevAll(\"form\").find(\"input\").val()});'>geselecteerde afbeelding gebruiken</button>";

?>
