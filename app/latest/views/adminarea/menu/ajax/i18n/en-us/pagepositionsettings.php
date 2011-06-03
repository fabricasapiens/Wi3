<?php
    //buffer content of the 'general' tab
    ob_start();
?>

<?php 
    
    // [!] This page is currently skipped when clicking a pageposition
    
    echo "<div id='wi3_edited_page' style='display: none;'>" . $page->id . "</div>";
    
    if (count($page->pages) == 1)
    {
        echo "<p>There is 1 page within this menuposition.</p>";
    }
    else
    {
        echo "<p>There are " . count($page->pages) . " pages within this menuposition.</p>";
    }
    
    $counter = 0;
    echo "<table>";
    foreach($page->pages as $realpage)
    {
        $counter++;
        if ($counter == 1)
        {
            echo "<tr><th>nr</th><th>titel</th>";
            $realpage->versiontags = Wi3::inst()->model->factory("site_array")->setname("versiontags")->setref($realpage)->load();
            //var_dump($realpage->versiontags->language);
            foreach($realpage->versiontags as $versiontagkey => $versiontagvalue)
            {
                echo "<th>".$versiontagkey."</th>";
            }
            echo "</tr>";
        }
        echo "<tr><td>" . $counter . ".</td><td><a href='javascript:void(0)' onClick='wi3.request(\"adminarea_menu_ajax/startEditPageSettings\", {pageid:\"" . $realpage->id . "\"});'>" . $realpage->longtitle . "</a></td>";
        foreach($realpage->versiontags as $versiontagkey => $versiontagvalue)
        {
            echo "<td>".$versiontagvalue."</td>";
        }
        echo"</tr>";
    }
    
    //end of general tab buffer
    $content_general = ob_get_contents();
    ob_end_clean();

?>

    <ul>
		<li><a href="#general">Pagina's</a></li>
	</ul>
	<div id="general">
		<?php echo $content_general; ?>
	</div>
