<?php

    $prefix = "component_simpleblog";

    // The component needs two tabs: one for a new blog-item, and one for the existing items. A third could be used for general layout-settings.
    ?>
    <div id="<?php echo $prefix; ?>_edit_tabs">
	<ul>
		<li><a href="#<?php echo $prefix; ?>_edit_tabs-1">Nieuw artikel</a></li>
		<li><a href="#<?php echo $prefix; ?>_edit_tabs-2">Bestaande artikelen</a></li>
	</ul>
	<div id="<?php echo $prefix; ?>_edit_tabs-1">
        <?php
        echo "<form onsubmit='return false;'>";

            // New article

            // Title
            echo Wi3::inst()->formbuilder->input()->attr("name", "title")->render();
            echo Wi3::inst()->formbuilder->input()->attr("name", "title")->render();

            $imagedata = Wi3::inst()->model->factory("site_data")->setref($field)->setname("image")->load();
            $fileid = $imagedata->data;
            echo Wi3::inst()->formbuilder->fileselector()->attr("name", "image")->set("extensions", Array("jpg", "jpeg"))->addextension("png")->set("selected", $fileid)->render();

        echo "</form>";

        echo "<button onclick='wi3.request(\"pagefiller_default_component_simpleblog/edit\", {fieldid: " . $field->id . ", image: $(this).prevAll(\"form\").find(\"input\").val()});'>geselecteerde afbeelding gebruiken</button>";

    echo "</div>";

    echo "<div id='" . $prefix . "_edit_tabs-2'>";
        echo "hoi";
    echo "</div>";

?>

<script>
    $("#<?php echo $prefix; ?>_edit_tabs").tabs();
</script>
