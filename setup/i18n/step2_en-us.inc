<h1>writability of folders</h1>
<p>
Wi3 requires some folders to be writable. More specific, this concerns:
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
You should manually ensure that all of the above folders are writable and then press 'continue'.
</p>
<p> </p>
<form method='POST'>
   <input type='hidden' name='step' value='3' />
   <input type='submit' value='Continue' />
</form>
