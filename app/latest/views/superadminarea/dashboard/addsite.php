<form method='POST' action='<?php echo Wi3::inst()->urlof->action('createsite');?>'>
    <h2>Algemeen</h2>
    <input class='rightside' name='name'></input><label>naam</label><br />
    <input class='rightside' name='title'></input><label>titel</label><br />
    <label>direct actief? </label><select class='rightside' name='active'><option value='0'>nee</option><option value='1'>ja</option></select><br />
    <h2>Database</h2>
    <label for='dbusername'>Gebruikersnaam</label><input class='rightside' name='dbusername' /><br />
    <label for='dbpassword'>Wachtwoord</label><input class='rightside' name='dbpassword' /><br />
    <p> </p>
    <p>
    Elke site heeft een <strong>eigen database</strong> nodig.
    </p>
    <p>
    Moet Wi3 een bestaande database gebruiken, of zelf een nieuwe aanmaken met bovenstaande login?
    </p>
    <input type='radio' name='dbexistingornew' value='existing'>Bestaand <small>(Bestaande tabellen binnen de database zullen overschreven worden!)</small></input><br />
    <input type='radio' name='dbexistingornew' value='new'>Nieuw <small>(Bovenstaande gebruiker dient de rechten te hebben om een database aan te maken!)</small></input><br />
    <p> </p>
    <label for='dbname'>Databasenaam</label><input class='rightside' name='dbname' /><br />
    <p> </p>
    <button>Aanmaken</button>
</form>
