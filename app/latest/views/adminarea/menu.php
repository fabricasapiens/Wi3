<?php
    //we need the UI and fancybox plugin
    Wi3::inst()->plugins->load("plugin_jquery_ui"); // For dragging and dropping
    Wi3::inst()->plugins->load("plugin_jquery_fancybox"); // For the nice modal box
    
    $site = Wi3::inst()->sitearea->site;
     
    echo "<div id='wi3_prullenbak'>";
        echo "<div id='prullenbak_onder'><h2>Sleep hier om te verwijderen</h2></div>";
    echo "</div>";
    echo "<div id='wi3_add_pages'>";
        echo "<a href='javascript:void(0);' onClick='$(this).next().slideToggle();'><h2>Nieuwe pagina</h2></a>";
        echo "<div style='display:none; margin:15px; margin-right: 55px;'>";
        echo View::factory("adminarea/menu/addpageposition");
        echo "</div>";
    echo "</div>";
    
    // Load pages of the site
    $list = Wi3::inst()->model->factory("site_pageposition")->roots("1"); // Scope 1
    // TODO: how can we preload the whole tree, and only then traverse through the tree?
    
    echo "<ul id='menu_pages' style='position: relative;' class='simpleTree'><li class='root'><span></span><ul>";
    if (count($list) > 0)
    {
        foreach($list as $pageposition) {
            // Get all pages for this pageposition, and display the title of the first page 
            // TODO: get a list of tag-'preferences' from the versionplugins, and get those pages that have the highest match on these tags
            $pages = $pageposition->pages;
            foreach($pages as $page)
            {
                $title = $page->longtitle;
                break;
            }
            echo "<li class='treeItem' id='treeItem_" . $pageposition->id . "'><span>" . html::anchor(Wi3::inst()->urlof->action("adminarea", "content") . $pageposition->id, $title) . "</span>";
            echo render_children_as_list($pageposition);
            echo "</li>";
        }
    }
    echo "</ul></li></ul>";
    
    //render the container in which the page properties will appear when a page is single-clicked
    echo "<div id='menu_pagesettings'>";
         echo "<div id='menu_pagesettings_tabs'>";
            
        echo "</div>";
    echo "</div>";
    
    // Kohana::$log->add(Kohana::ERROR, "render as list");
    // exit;
    
    function render_children_as_list($page) {
        //recursive
        $ret = "";
        if ($page->has_children()) {
            $ret .= "<ul>";
            $children = $page->children();
            foreach($children as $child) {
                // Get all pages for this pageposition, and display the title of the first page 
                // TODO: get a list of tag-'preferences' from the versionplugins, and get those pages that have the highest match on these tags
                $pages = $child->pages;
                foreach($pages as $page)
                {
                    $title = $page->longtitle;
                    break;
                }
                //render as link, although Javascript will remove the href and will ceate a DblClick and OnClick for the pages
                $ret .= "<li class='treeItem' id='treeItem_" . $child->id . "'><span>" . html::anchor(Wi3::inst()->urlof->action("adminarea", "content") . $child->id, $title) . "</span>";
                $ret .= render_children_as_list($child);
                $ret .= "</li>";
            }
            $ret .= "</ul>";
        }
        return $ret;
    }
    
?>
