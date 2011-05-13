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
                        echo "<span class='rightside'>" . $url->url . " (" . HTML::anchor( trim($url->url. "/") . "adminarea", "adminarea") . ")</span><br />";
                    }
                
                ?>
            </p>
            <form method='POST' action='<?php echo Wi3::inst()->urlof->action("addurl");?>'>
                <input type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
                <label>Nieuwe URL</label><input class='rightside' name='url' value=''></input><br />
                <label style='visibility: hidden;'>.</label><div style='display: inline-block;' class='rightside'><button>Nieuwe URL toevoegen</button></div>
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
