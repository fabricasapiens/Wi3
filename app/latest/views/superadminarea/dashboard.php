<h1>Nieuwe site aanmaken</h1>
<form method='POST' action='<?php echo Wi3::inst()->urlof->action('createsite');?>'>
    <label>naam/map</label><input name='name'></input><br />
    <label>titel</label><input name='title'></input><br />
    <label>actief </label><select name='active'><option value='0'>nee</option><option value='1'>ja</option></select>
    <button>aanmaken</button>
</form>

<h1>Site beheer</h1>
<?php 

    // The , FALSE parameter sets no limit to the amount of records loaded
    $sites = Wi3::inst()->model->factory("site")->load(NULL, FALSE);
    foreach($sites as $site) 
    {
    
?>
<h2><?php echo $site->title." (".$site->name.")"; ?></h2>
<form method='POST' action='<?php echo Wi3::inst()->urlof->action(($site->active ? "deactivatesite" : "activatesite"));?>'>
    <input type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
    <button><?php echo ($site->active ? "deactiveren" : "activeren"); ?></button>
</form>
<form method='POST' action='<?php echo Wi3::inst()->urlof->action('deletesite');?>'>
    <input type='hidden' name='name' value='<?php echo $site->name; ?>'></input>
    <button>verwijderen</button> <strong>LET OP: </strong>Dit verwijdert de complete site, inclusief databases en bestanden!
</form>

<?php

    }
    
?>