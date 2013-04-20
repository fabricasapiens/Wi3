<?php
	$prefix = "pagefiller_default_component_link_edit_";
?>

<form onsubmit='return false;' class='<?php echo $prefix; ?>tabs'>
<ul>
	<li><a href="#<?php echo $prefix; ?>file">Bestand</a></li>
    <li><a href="#<?php echo $prefix; ?>page">Pagina</a></li>
    <li><a href="#<?php echo $prefix; ?>url">Vrije URL</a></li>
</ul>

<?php

	// Load the data that is associated with this field
    $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();

	echo "<div id='" . $prefix . "url'>";
	    echo Wi3::inst()->formbuilder->input()->attr("name", "url")->attr("value", $data->url)->render();
		echo "<button onclick='wi3.request(\"pagefiller_default_component_link/edit\", {fieldid: \"" . $field->id . "\", url: $(this).closest(\"form\").find(\"[name=url]\").val()});'>url gebruiken</button>";
	echo "</div>";

	echo "<div id='" . $prefix . "file'>";
		echo "<div onClick='wi3.request(\"pagefiller_default_component_link/edit\", {fieldid: \"" . $field->id . "\", fileid: $(this).find(\"[name=file]\").val()});'>";
			if (!isset($data->fileid)) {
				$data->fileid = false;
			}
			echo Wi3::inst()->formbuilder->fileselector()->attr("name", "file")->set("selected", $data->fileid)->render();
		echo "</div>";
	echo "</div>";

	echo "<div id='" . $prefix . "page'>";
		echo "<div onClick='wi3.request(\"pagefiller_default_component_link/edit\", {fieldid: \"" . $field->id . "\", pageid: $(this).find(\"[name=page]\").val()});'>";
			if (!isset($data->pageid)) {
				$data->pageid = false;
			}
			echo Wi3::inst()->formbuilder->pageselector()->attr("name", "page")->set("selected", $data->pageid)->render();
		echo "</div>";
	echo "</div>";

?>

</form>
