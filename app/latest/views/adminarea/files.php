<?php

    // We need the UI and fancybox plugin
    Wi3::inst()->plugins->load("plugin_jquery_ui"); // For dragging and dropping
    Wi3::inst()->plugins->load("plugin_jquery_fancybox"); // For the nice modal box
    
    $site = Wi3::inst()->sitearea->site;
    
    echo "<div id='wi3_prullenbak'>";
        echo "<div id='prullenbak_onder'><h2>Sleep hier om te verwijderen</h2></div>";
    echo "</div>";
    echo "<div id='wi3_add_file'>";
        if (isset($message)) { echo "<div style='color: #cc0000; padding: 15px;'>" . $message . "</div>"; }
        echo "<a href='javascript:void(0);' onClick='$(this).next().slideToggle();'><h2>Bestand toevoegen</h2></a>";
        echo "<div style='display:none; margin:15px; margin-right: 55px;'>";
        echo View::factory("adminarea/files/addfile");
        echo "</div>";
    echo "</div>";
    echo "<div id='wi3_add_folder'>";
        echo "<a href='javascript:void(0);' onClick='adminarea.addfolder();'><h2>+</h2></a>";
    echo "</div>";

    function getextension($filename) {
        $lastdotpos = strrpos($filename, ".");
        return strtolower(substr($filename, $lastdotpos+1));
    }

    function isimage($file) {
        $extensions = Array("jpg", "jpeg", "png", "bmp", "gif");
        return $file->type === "file" && in_array(getextension($file->filename), $extensions);
    }
    
    echo "<ul id='files_files' style='position: relative;' class='simpleTree'><li class='root'><span></span><ul>";
    // Get all files and render them in a tree
    $files = Wi3::inst()->sitearea->files->getall();
    if (count($files) > 0)
    {
        $prevfile = NULL;
        foreach($files as $file)
        {

            // If there is a previous file, we can check if we went up or down in the tree
            if ($prevfile != NULL)
            {
                if ($file->{$prevfile->level_column} > $prevfile->{$prevfile->level_column})
                {
                    // Going a level deeper
                    echo "<ul>";
                }
                else if ($file->{$prevfile->level_column} < $prevfile->{$prevfile->level_column})
                {
                    // Going a level up, or maybe even more than 1 level 
                    // Find out how many levels we go up and close every level properly
                    for($i=($prevfile->{$prevfile->level_column} - $file->{$prevfile->level_column}); $i > 0; $i--)
                    {
                        echo "</li></ul></li>";
                    }
                } 
                else 
                {
                    echo "</li>";
                }
            }
            $prevfile = $file;
            
            echo "<li class='treeItem " . ($file->type == "folder" ? "permanent-folder" : "") . "' id='treeItem_" . $file->id . "'>";
            echo "<span>" . html::anchor(Wi3::inst()->urlof->site . "_uploads/" . $file->filename, (isimage($file) ? "<img src='" . Wi3::inst()->urlof->site . "_uploads/30/" . $file->filename . "'/> " : "") . $file->title) . "</span>";
        }
        // Now, if we have ended far from root (i.e. a deep node), we need to add some </li></ul>
        if ($file->{$prevfile->level_column} > 0)
        {
            for($i=$file->{$prevfile->level_column}; $i > 0; $i--)
            {
                echo "</li></ul>";
            }
        }
        echo "</li></ul>";
    }
    echo "</ul>";
    
    //render the container in which the page properties will appear when a page is single-clicked
    echo "<div id='files_filesettings'>";
         echo "<div id='files_filesettings_tabs'>";
            
        echo "</div>";
    echo "</div>";
    
    
?>
