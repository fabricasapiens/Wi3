<?php
    //buffer content of the 'general' tab
    ob_start();
?>

<?php 

    //aangeven welke pagine op dit moment gewijzigd wordt 
    echo "<div id='wi3_edited_file' style='display: none;'>" . $file->id . "</div>";
    echo "<form id='wi3_fileedit_form' onsubmit='wi3.request(\"ajaxengine/editFileSettings/\" + $(\"#wi3_edited_file\").html(), $(\"#wi3_fileedit_form\").serializeArray()); return false;'>";
    
    echo "<h2>Open file</h2>";
    echo "<p><label for='link'>links: </label><span name='viewlink'><a target='_blank' href='" . Wi3::inst()->urlof->site . "_uploads/" . $file->filename . "'>open</a></span></p>";
    
    echo "<h2>File settings</h2>";
    Wi3::inst()->acl->grant("admin", $file);
    // TODO: proper checking for the right to edit this file (= every admin OR when somebody has the editright for the file)
    try 
    {
        Wi3::inst()->acl->check($file);
        echo "<p><label for='title'>title: </label><input name='title' id='title' type='text' value='" . $file->title . "' /></p>";
        // A sprig boolean value will always revert to either TRUE or FALSE, so we can only work with those values, and use external variables for 0 and 1
        //$visible = ($file->visible===TRUE?"1":"0");
        //echo "<p><label for='visible'>zichtbaarheid in listings </label><select name='visible' id='visible' value='" . $visible . "'>" . Wi3::inst()->optionlist(array("1"=>"zichtbaar", "0"=>"verborgen"),$visible) . "</select></p>";
    } 
    catch(Exception $e) 
    {
        // Well, no edit-rights obviously
        echo "<p>You don't have the proper rights to edit file settings.</p>";
    }
    
    //check whether user is allowed to set the rights of a file
    //one would need to be admin for that. (that means: admin, siteadmin, having the 'adminright' or being the owner of the file)
    echo "<h2>Rights settings</h2>";
    try 
    {
        Wi3::inst()->acl->check($file);
        //so this user has admin rights, show the different rights
        echo "<p>
        <label for='viewright'>for viewing: </label><input name='viewright' id='viewright' type='text' value='" . $file->viewright . "' /><br />
        <label for='editright'>for editing: </label><input name='editright' id='editright' type='text' value='" . $file->editright . "' /><br />
        <label for='adminright'>for admin: </label><input name='adminright' id='adminright' type='text' value='" . $file->adminright . "' /><br />
        </p>";
    }
    catch(Exception $e) 
    {
        echo "<p>You don't have the proper rights to edit rights settings</p>";
    }
    
    function printoptions($tree, $activeid) {
        $ret = "";
        if (!empty($tree)) {
            foreach($tree as $child) {
                $ret .= "<option value='" . $child->id . "' "; 
                if ($child->id == $activeid) { $ret .= "selected='selected'"; }
                $ret .= ">" . $child->title . "</option>";
                $ret .= printoptions($child->children, $activeid);
            }
        }
        return $ret;
    }
    
    echo "</form>";
    
    //opslaan knop
    echo " <button onClick='wi3.request(\"adminarea_files_ajax/editFileSettings/\" + $(\"#wi3_edited_file\").html(), $(\"#wi3_fileedit_form\").serializeArray());'>save</button>";
    
    //end of general tab buffer
    $content_general = ob_get_contents();
    ob_end_clean();
    
    //now render the tabs
?>

    <ul>
		<li><a href="#general">General</a></li>
		<li style='display: none;'><a href="#export">Export</a></li>
	</ul>
	<div id="general">
		<?php echo $content_general; ?>
	</div>
	<div id="export">
		<p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
		<p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
	</div>
