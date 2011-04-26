<h1>Nieuwe site aanmaken</h1>
<form method='POST' action='<?php echo Wi3::inst()->urlof->action('createsite');?>'>
    <input class='rightside' name='name'></input><label>naam/map</label><br />
    <input class='rightside' name='title'></input><label>titel</label><br />
    <label>actief </label><select class='rightside' name='active'><option value='0'>nee</option><option value='1'>ja</option></select>
    <br />
    <button>Aanmaken</button>
</form>

<h1>Bestaande sites</h1>
<?php 

    // The , FALSE parameter sets no limit to the amount of records loaded
    $sites = Wi3::inst()->model->factory("site")->load(NULL, FALSE);
    foreach($sites as $site) 
    {
    
        ?>
        <div class='topbottomborderedbox'>
            <h2><?php echo $site->title; ?></h2>
            <p>Map: <span class='rightside'><?php echo Wi3::inst()->pathof->site($site->name); ?></span></p> 
            <form method='POST' action='<?php echo Wi3::inst()->urlof->action(($site->active ? "deactivatesite" : "activatesite"));?>'>
                <input  type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
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
