<?php
    //buffer content of the 'general' tab
    ob_start();
?>

<?php 

    //aangeven welke pagine op dit moment gewijzigd wordt 
    echo "<div id='wi3_edited_page' style='display: none;'>" . $page->id . "</div>";
    echo "<form id='wi3_pageedit_form' onsubmit='wi3.request(\"ajaxengine/editPageSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_form\").serializeArray()); return false;'>";
    
    echo "<h2>Pagina bekijken en wijzigen</h2>";
    echo "<p><label for='link'>links: </label><span name='viewlink'><a target='_blank' href='" . Wi3::inst()->urlof->page($page) . "'>bekijken</a></span> <span name='editlink'><a href='" . Wi3::inst()->urlof->action("adminarea", "content") . "_" . $page->id . "'>wijzigen</a></span></p>";
    
    echo "<h2>Pagina-instellingen</h2>";
    Wi3::inst()->acl->grant("admin", $page);
    // TODO: proper checking for the right to edit this page (= every admin OR when somebody has the editright for the page)
    try 
    {
        Wi3::inst()->acl->check($page);
        echo "<p><label for='pagetitle'>paginatitel: </label><input name='longtitle' id='longtitle' type='text' value='" . $page->longtitle . "' /></p>";
        // A sprig boolean value will always revert to either TRUE or FALSE, so we can only work with those values, and use external variables for 0 and 1
        $visible = ($page->visible===TRUE?"1":"0");
        echo "<p><label for='visible'>zichtbaar in menu: </label><select name='visible' id='visible' value='" . $visible . "'>" . Wi3::inst()->optionlist(array("1"=>"zichtbaar", "0"=>"verborgen"),$visible) . "</select></p>";
    } 
    catch(Exception $e) 
    {
        // Well, no edit-rights obviously
        echo "<p>U hebt niet de benodige rechten om de pagina-instellingen aan te passen.</p>";
    }
    
    //check whether user is allowed to set the rights of a page
    //one would need to be admin for that. (that means: admin, siteadmin, having the 'adminright' or being the owner of the page)
    echo "<h2>Rechten-instellingen</h2>";
    try 
    {
        Wi3::inst()->acl->check($page);
        //so this user has admin rights, show the different rights
        echo "<p>
        <label for='viewright'>voor bekijken: </label><input name='viewright' id='viewright' type='text' value='" . $page->viewright . "' /><br />
        <label for='editright'>voor wijzigen: </label><input name='editright' id='editright' type='text' value='" . $page->editright . "' /><br />
        "; //<label for='adminright'>voor admin: </label><input name='adminright' id='adminright' type='text' value='" . $page->adminright . "' /><br />
        echo "</p>";
    }
    catch(Exception $e) 
    {
        echo "<p>U hebt niet de benodige rechten om de rechten-instellingen aan te passen.</p>";
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
    echo " <button onClick='wi3.request(\"adminarea_menu_ajax/editPageSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_form\").serializeArray());'>opslaan</button>";
    
    //end of general tab buffer
    $content_general = ob_get_contents();
    ob_end_clean();

    //--- 
    // The template part
    // ---
    ob_start();
    //one would need to have edit-rights to edit the template-settings of the page
    echo "<form id='wi3_pageedit_template_form' onsubmit='wi3.request(\"ajaxengine/editPageTemplateSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_template_form\").serializeArray()); return false;'>";
    echo "<h2>Template instellingen</h2>";
    try 
    {
        Wi3::inst()->acl->check($page);
        // Ask the pagefiller to show its template-options
        // Indeed, different pagefillers can have different options for this
        $pagefillername = "Pagefiller_" . $page->filler;
        $pagefiller = new $pagefillername();
        echo $pagefiller->pageoptionstemplatehtmlfortemplate($page->templatename);
    }
    catch(Exception $e) 
    {
        echo "<p>U hebt niet de benodige rechten om de template-instellingen aan te passen.</p>";
    }
    echo "</form>";
    //save button
    echo " <button onClick='wi3.request(\"pagefiller_default_ajax/editPageTemplateSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_template_form\").serializeArray());'>opslaan</button>";
    $content_template = ob_get_contents();
    ob_end_clean();
    
    // Redirect part
    // Doorverwijzen naar externe link of naar een andere pagina
    ob_start();
    //one would need to have edit-rights to edit the template-settings of the page
    echo "<form id='wi3_pageedit_redirect_form' onsubmit='wi3.request(\"adminarea_menu_ajax/editPageRedirectSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_redirect_form\").serializeArray()); return false;'>";
    echo "<h2>Doorverwijzingen</h2>";
    try 
    {
        Wi3::inst()->acl->check($page);
        echo "<p>";
        echo "<select style='width: 300px;' name='redirect_type' id='redirect_type' value='" . $page->redirecttype . "'>" . Wi3::optionlist(array("none"=>"Geen doorverwijzing; eigen inhoud pagina", "wi3" => "Doorverwijzen naar pagina binnen site", "external"=>"Doorverwijzen naar externe URL"), $page->redirecttype) . "</select><br />";

        $pagelist = Array();
        $pagepositions = Wi3::inst()->model->factory("site_pageposition")->load(NULL, FALSE); // False for no limit
        foreach($pagepositions as $pageposition)
        {
            // Get level
            $level = $pageposition->lvl;
            $prefix = "";
            for($i=0;$i<$level;$i++)
            {
                $prefix .= "-";
            }
            // Get first page under the pageposition 
            $pages = $pageposition->pages;
            foreach($pages as $normalpage)
            {
                break;
            }
            $pagelist[$normalpage->id] = $prefix . " " . $normalpage->longtitle;
        }
        /*
        function pagelist($cpage) 
        {
            global $pagelist;
            foreach($cpage->children as $p) 
            {
                $pagelist[$p->id] = $p->title;
                if (is_array($p->children) AND !empty($p->children)) {
                    pagelist($p);
                }
            }
        }
        pagelist($pages);
        */
        echo "</p><p id='redirect_wi3' " . ($page->redirecttype!="wi3"?" style='display:none;'":"") . ">";
        echo "<label for='redirect_wi3'>Doorverwijzen naar pagina </label><select name='redirect_wi3' value='" . $page->redirect_wi3 . "'>" . Wi3::optionlist( $pagelist, $page->redirect_wi3) . "</select></p>";
        echo "<p id='redirect_external' " . ($page->redirecttype!="external"?" style='display:none;'":"") . ">";
        echo "<label for='redirect_external'>Doorververwijzen naar URL </label><input type='text' name='redirect_external' value='" . $page->redirect_external. "' />";
        echo "</p>";
    }
    catch(Exception $e) 
    {
        throw($e);
        echo "<p>U hebt niet de benodige rechten om een doorverwijzing in te stellen.</p>";
    }
    echo "</form>";
    //save button
    echo " <button onClick='wi3.request(\"adminarea_menu_ajax/editPageRedirectSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_redirect_form\").serializeArray()); return false;'>opslaan</button>";
    $content_redirect = ob_get_contents();
    ob_end_clean();
    
    //now render the tabs
?>

    <ul>
		<li><a href="#general">Algemeen</a></li>
		<li><a href="#template">Template</a></li>
        <li><a href="#redirect">Doorverwijzing</a></li>
		<li style='display: none;'><a href="#export">Exporteren</a></li>
	</ul>
	<div id="general">
		<?php echo $content_general; ?>
	</div>
	<div id="template">
        <?php echo $content_template; ?>
	</div>
    <div id="redirect">
        <?php echo $content_redirect; ?>
	</div>
	<div id="export">
		<p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
		<p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
	</div>
