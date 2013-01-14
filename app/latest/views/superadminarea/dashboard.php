<?php

    Wi3::inst()->plugins->load("plugin_jquery_core"); // For the nice modal box

    echo "<div id='wi3_add_pages' style='width: 920px'>";
        echo "<a href='javascript:void(0);' onClick='$(this).next().slideToggle();'><h2>Nieuwe site</h2></a>";
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
            echo "<p>Uw root .htaccess is niet schrijfbaar..! ";
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/.htaccess"))
            {
                echo "Het bestaande .htaccess bestand dient een aantal specifieke regels voor Wi3 te bevatten om het cms correct te laten functioneren. ";
            }
            else
            {
                echo "Een .htaccess in de www-root is echter nodig om Wi3 correct te laten functioneren. ";
            }
            echo "U kunt Wi3 toestaan het .htaccess bestand automatisch correct aan te passen door de .htaccess schrijfbaar te maken. Als u dit niet wilt, dient u handmatig te zorgen dat het .htaccess bestand in uw www-root de regels bevat zoals te vinden in " . HTML::anchor( Wi3::inst()->urlof->action("htaccessrules"), "dit bestand") . ".";
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
            <p>Map: <span class='rightside'><?php echo Wi3::inst()->pathof->site($site->name); ?></span></p>
            <p>URLs
                <?php
                
                    foreach($site->urls as $url)
                    {
                        echo "<form method='POST' action='" . Wi3::inst()->urlof->action('detachurl') . "'>";
                            echo "<input type='hidden' name='url' value='" . $url->url . "'></input>";
                            echo "<input type='hidden' name='name' value='" . $site->name . "'></input>";
                            echo "<span class='rightside'>" . $url->url . " (" . HTML::anchor( trim($url->url, "/"). "/adminarea", "adminarea") . ") <button>ontkoppelen</button></span><br />";
                        echo "</form>";
                    }
                
                ?>
            </p>
            <form method='POST' action='<?php echo Wi3::inst()->urlof->action("addurl");?>'>
                <input type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
                <label>Nieuwe URL</label><input class='rightside' name='url' value=''></input><br />
                <label style='visibility: hidden;'>.</label><div style='display: inline-block;' class='rightside'><button>Nieuwe URL toevoegen</button></div>
            </form>
            <form method='POST' action='<?php echo Wi3::inst()->urlof->action("resetadminpassword");?>'>
                <input type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
                <label>Wachtwoord</label><input class='rightside' name='adminpassword' value=''></input><br />
                <label style='visibility: hidden;'>.</label><div style='display: inline-block;' class='rightside'><button>Admin wachtwoord zetten</button></div>
            </form>
            <form method='POST' action='<?php echo Wi3::inst()->urlof->action(($site->active ? "deactivatesite" : "activatesite"));?>'>
                <input type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
                <label>Status van site</label><div style='display: inline-block;' class='rightside'> <?php echo ($site->active ? "actief" : "niet actief"); ?> <button><?php echo ($site->active ? "deactiveren" : "activeren"); ?></button></div>
            </form>
            <form method='POST' action='<?php echo Wi3::inst()->urlof->action('deletesite');?>'>
                <input type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
                <label>Verwijderen</label><div style='display: inline-block;' class='rightside'><button>verwijderen</button> <strong>LET OP: </strong>Dit verwijdert de complete site, inclusief databases en bestanden!</div>
            </form>
        </div>

        <?php

    }
    
?>
