<?php 

    echo "<form onsubmit='return false;'>";
    
        $currentemailaddress = $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("emailaddress")->load();
        $emailinput = Wi3::inst()->formbuilder->input()->attr(Array("name"=>"emailaddress", "value" => $currentemailaddress));
        echo $emailinput->render();
        
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

    echo "<button onclick='wi3.request(\"pagefiller_default_component_imageshop/editImage\", {fieldid: " . $field->id . ", image: $(this).prevAll(\"form\").find(\"input\").val()});'>geselecteerde afbeeldingen gebruiken</button>";

?>
