<?php

    Wi3::inst()->plugins->load("plugin_jquery_core"); // For the nice modal box

    echo "<div id='wi3_add_pages' style='width: 920px'>";
        echo "<a href='javascript:void(0);' onClick='$(this).next().slideToggle();'><h2>New site</h2></a>";
        echo "<div style='display:none; margin:15px; margin-right: 55px; position: relative;'>";
        echo View::factory("superadminarea/dashboard/addsite");
        echo "</div>";
    echo "</div>";

?>
<p></p>
<?php

    // If there is a URL (and thus a site), we need to alert the user if the .htaccess in the root cannot be properly written
    $all = Wi3::inst()->model->factory("url")->load(NULL, FALSE); // FALSE for no limit = load all
    if (count($all) > 0 AND !is_writable($_SERVER["DOCUMENT_ROOT"]."/.htaccess"))
    {
        // Show the .htaccess rules for in the root
        echo "<div style='border: 1px solid #ff0000; padding: 20px;'>";
            echo "<h2>Belangrijk!</h2>";
            echo "<p>Your root .htaccess is not writable..! ";
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/.htaccess"))
            {
                echo "The existing .htaccess file should contain a specific set of rules for Wi3 to make the cms function correctly. ";
            }
            else
            {
                echo "However, a .htaccess file in the www-root is a prerequisite to make Wi3 function correctly. ";
            }
            echo "You can allow Wi3 to automatically correct the .htaccess file by making the .htaccess file writable. If you do not wish to do so, you should manually ensure that the .htaccess file in the www-root contains the rules as found in " . HTML::anchor( Wi3::inst()->urlof->action("htaccessrules"), "this file") . ".";
        echo "</div>";
    }

?>
<?php 

    // The , FALSE parameter sets no limit to the amount of records loaded
    $sites = Wi3::inst()->model->factory("site")->load(NULL, FALSE);
    foreach($sites as $site) 
    {
    
        ?>
        <div class='topbottomborderedbox'>
            <h2><?php echo $site->title; ?></h2>
            <p>Folder: <span class='rightside'><?php echo Wi3::inst()->pathof->site($site->name); ?></span></p>
            <p>URLs
                <?php
                
                    foreach($site->urls as $url)
                    {
                         echo "<form method='POST' action='" . Wi3::inst()->urlof->action('detachurl') . "'>";
                            echo "<input type='hidden' name='url' value='" . $url->url . "'></input>";
                            echo "<input type='hidden' name='name' value='" . $site->name . "'></input>";
                            echo "<span class='rightside'>" . $url->url . " (" . HTML::anchor( trim($url->url, "/"). "/adminarea", "adminarea") . ") <button>detach</button></span><br />";
                        echo "</form>";
                    }
                
                ?>
            </p>
            <form method='POST' action='<?php echo Wi3::inst()->urlof->action("addurl");?>'>
                <input type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
                <label>New URL</label><input class='rightside' name='url' value=''></input><br />
                <label style='visibility: hidden;'>.</label><div style='display: inline-block;' class='rightside'><button>Add new URL</button></div>
            </form>
            <form method='POST' action='<?php echo Wi3::inst()->urlof->action(($site->active ? "deactivatesite" : "activatesite"));?>'>
                <input type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
                <label>Status of site</label><div style='display: inline-block;' class='rightside'> <?php echo ($site->active ? "active" : "non active"); ?> <button><?php echo ($site->active ? "deactivate" : "activate"); ?></button></div>
            </form>
            <form method='POST' action='<?php echo Wi3::inst()->urlof->action('deletesite');?>'>
                <input type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
                <label>Delete</label><div style='display: inline-block;' class='rightside'><button>delete</button> <strong>CAUTION: </strong>This removes the complete site, including the complete database and all files!</div>
            </form>
        </div>

        <?php

    }
    
?>
