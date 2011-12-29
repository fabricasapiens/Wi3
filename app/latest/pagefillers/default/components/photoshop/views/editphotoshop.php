<?php 

    echo "<form onsubmit='return false;'>";
        
        $fileselector = Wi3::inst()->formbuilder->fileselector()->attr("name", "image")->set("extensions", Array("jpg", "jpeg"))->addextension("png");

        $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("images")->load();
        if (!empty($imagedata) AND !empty($imagedata->data)) {
            $array = unserialize($imagedata->data);
            foreach($array as $id => $information) {
            	$fileid = $imagedata->data;
	            echo $fileselector->set("selected", $fileid);
            }
        }
        echo $fileselector->render();
    
    echo "</form>";

    echo "<button onclick='wi3.request(\"pagefiller_default_component_photoshop/editImage\", {fieldid: " . $field->id . ", image: $(this).prevAll(\"form\").find(\"input\").val()});'>geselecteerde afbeeldingen gebruiken</button>";

?>
