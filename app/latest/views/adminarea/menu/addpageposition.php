<form id='wi3_adminarea_menu_addpageform' onSubmit=''>
    <label>Paginatitel:</label>
    <input name='longtitle'/>
<?php

    // No matter how great this mechanism might be, it's not going to be used for now...
	/*
    $versionhtml = "";
    foreach(Wi3::inst()->sitearea->pages->versionplugins() as $plugin)
    {
        $versionhtml .= $plugin->versionhtmlforaddpage();
    }

    if (!empty($versionhtml))
    {
        echo $versionhtml;
    }
    */

    echo "<div id='menu_addpageoptions'>";
    // Get pagefillers, and display the addpageoptions of the default one, along with a choice to pick another
    $pagefillers = Wi3::inst()->configof->wi3->pagefillers->pagefillers;
    if (isset($pagefillers->default))
    {
        // Display the addpageoptions of the default pagefiller
        // $pagefillerpath = $pagefillers->default->path."classes/pagefiller/default.php";
        // include($pagefillerpath);
        $pagefiller = new Pagefiller_default();
        echo $pagefiller->pageoptionshtml();
    }
    echo "</div>";


?>
</form>
<button style='width: 100%; margin-bottom: 25px;' onClick='adminarea.addpageposition();'>Aanmaken</button>
