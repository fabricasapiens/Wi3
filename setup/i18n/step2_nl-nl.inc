<h1>Schrijfbaarheid van mappen</h1>
<p>
Het is voor Wi3 noodzakelijk dat bepaalde mappen schrijbaar zijn. Het gaat om de volgende mappen:
</p>
<?php

    foreach($paths as $path => $writable)
    {
        if ($writable)
        {
            echo "<div class='rightside'>status: " . __("writable") . "</div><div>" . $path . "</div>";
        }
        else 
        {
            echo "<div class='rightside'>status: " . __("not writable") . "</div><div>" . $path . "</div>";
        }
    }

?>
<p>
U dient de mappen handmatig schrijfbaar te maken, waarna u op 'verder' klikt.
</p>
<p> </p>
<form method='POST'>
   <input type='hidden' name='step' value='3' />
   <input type='submit' value='Verder' />
</form>
