<h1>Database settings</h1>
<?php
    if (isset($message))
    {
        echo "<div id='message'>" . $message . "</div>";
    }
?>
<p>
Which username and password should Wi3 use for accessing the MySQL Database?
</p>
<form method='POST'>
   <input type='hidden' name='step' value='5' />
   <label for='dbusername'>Username</label><input name='dbusername' />
   <label for='dbpassword'>Password</label><input name='dbpassword' />
   <p>
   Should Wi3 use an existing database, or create a new one using above login details?
   </p>
   <input type='radio' name='dbexistingornew' value='existing'>Existing <small>(This will overwrite existing tables within the database!)</small></input><br />
   <input type='radio' name='dbexistingornew' value='new'>New <small>(Above user should have the rights to create a database!)</small></input>
   <label for='dbname'>Databasename</label><input name='dbname' />
   <p> </p>
   <input type='submit' value='Save and continue' />
</form>
