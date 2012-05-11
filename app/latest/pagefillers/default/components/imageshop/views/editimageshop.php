<?php 

    echo "<form onsubmit='return false;'>";
        echo "<p><label for='emailaddress' class='mediumpadding'>E-mailadres</label></p><p class='mediumpadding'>";
    
        $currentemailaddress = $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("emailaddress")->load();
        $emailinput = Wi3::inst()->formbuilder->input()->attr(Array("class" => "fullwidth", "name"=>"emailaddress", "value" => $currentemailaddress->data));
        echo $emailinput->render();
        
        echo "</p><p><label for='image' class='mediumpadding'>Afbeeldingenmap</label></p>";
        
        $folderselector = Wi3::inst()->formbuilder->folderselector()->attr("name", "image")->set("fileextensions", Array("jpg", "jpeg"))->addextension("png");

        $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("images")->load();
        if (!empty($imagedata) AND !empty($imagedata->data)) {
            $array = unserialize($imagedata->data);
            foreach($array as $id => $information) {
            	$fileid = $imagedata->data;
	            echo $fileselector->set("selected", $fileid);
            }
        }
        echo $folderselector->render();
    
    echo "</form>";

    echo "<button onclick='wi3.request(\"pagefiller_default_component_imageshop/editImage\", {fieldid: " . $field->id . ", emailaddress: $(this).prevAll(\"form\").find(\"input[name=emailaddress]\").val(), image: $(this).prevAll(\"form\").find(\"input[name=image]\").val()});'>Opslaan</button>";

?>
