<form method='POST' action='<?php echo Wi3::inst()->urlof->action('createsite');?>'>
    <h2>General</h2>
    <input class='rightside' name='name'></input><label>name</label><br />
    <input class='rightside' name='title'></input><label>title</label><br />
    <label>immediately active?</label><select class='rightside' name='active'><option value='0'>no</option><option value='1'>yes</option></select><br />
    <h2>Database</h2>
    <label for='dbusername'>Username</label><input class='rightside' name='dbusername' /><br />
    <label for='dbpassword'>Password</label><input class='rightside' name='dbpassword' /><br />
    <p> </p>
    <p>
    Every site uses <strong>its own database</strong>.
    </p>
    <p>
    Should Wi3 use an existing database, or create a new one with above credentials?
    </p>
    <input type='radio' name='dbexistingornew' value='existing'>Existing <small>(Existing tables within the database will be overwritten!)</small></input><br />
    <input type='radio' name='dbexistingornew' value='new'>New <small>(Above user should have rights to create the database!)</small></input><br />
    <p> </p>
    <label for='dbname'>Databasename</label><input class='rightside' name='dbname' /><br />
    <p> </p>
    <button>Create</button>
</form>
