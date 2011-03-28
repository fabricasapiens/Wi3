<h1>Nieuwe pagina</h1>
<form id='addpage_form' onSubmit=''>
<h2>Pagina vullen</h2>
<?php

$pagetypes = Wi3::inst()->configof->site->pagetypes->pagetypes;
    
    if (isset($pagetypes)) {
        echo "<div>als <select name='type'>";
        foreach($pagetypes as $index => $pagetype) {
            echo "<option value='".$index."'>".ucfirst($pagetype->title)."</option>";
        }
        echo "</select></div>";
    }

    if (!empty($versionhtml))
    {
        echo "<h2>Instellingen</h2>"; 
        echo "<div style='margin: 5px;'>";
        echo $versionhtml;
        echo "</div>";
    }

?>
paginatitel: <input name='longtitle'/>
</form>
<button onClick='wi3.request("adminarea_menu_ajax/addpage", $("#addpage_form").serializeArray());'>Aanmaken</button>