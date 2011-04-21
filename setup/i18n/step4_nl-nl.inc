<h1>Database instellingen</h1>
<?php
    if (isset($message))
    {
        echo "<div id='message'>" . $message . "</div>";
    }
?>
<p>
Wat zijn de gebruikersnaam en wachtwoord die Wi3 moet gebruiken voor de MySQL Database?
</p>
<form method='POST'>
   <input type='hidden' name='step' value='5' />
   <label for='dbusername'>Gebruikersnaam</label><input name='dbusername' />
   <label for='dbpassword'>Wachtwoord</label><input name='dbpassword' />
   <p>
   Moet Wi3 een bestaande database gebruiken, of zelf een nieuwe aanmaken met bovenstaande login?
   </p>
   <input type='radio' name='dbexistingornew' value='existing'>Bestaand <small>(Bestaande tabellen binnen de database zullen overschreven worden!)</small></input><br />
   <input type='radio' name='dbexistingornew' value='new'>Nieuw <small>(Bovenstaande gebruiker dient de rechten te hebben om een database aan te maken!)</small></input>
   <label for='dbname'>Databasenaam</label><input name='dbname' />
   <p> </p>
   <input type='submit' value='Opslaan en verder' />
</form>
