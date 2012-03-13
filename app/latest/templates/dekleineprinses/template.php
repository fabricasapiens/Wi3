<?php

   $page = Wi3::inst()->sitearea->page;
   $site = Wi3::inst()->sitearea->site;

   // First, load the CSS
   $this->css("reset.css");
   $this->css("style.css");
   
   // Load JQuery UI (also loads Jquery Core via dependencies)
   Wi3::inst()->plugins->load("plugin_jquery_ui");

?>

<html>
    <head>
        <title>Peuterspeelzaal De Kleine Prinses</title>
    </head>

    <body>
        <div id="container">
            <div id="header">
                <div id="images">
                    <img src="<?php echo Wi3::inst()->urlof->site . "_wi3files/templates/dekleineprinses/static/images/logo.png"; ?>"/>
                </div>
                <div id="navigation">
                    <?php
                        echo Wi3::inst()->sitearea->navigation->menu->render();
                    ?>
                    <div class="floatfix">.</div>
                </div>  
            </div>
            <div id="content" class="mediumpadding">
                <cms type='editableblock' name='content'><p>Klik binnen deze tekst om de tekst te wijzigen.</p></cms>
                <div class="floatfix">.</div>
            </div>
        </div>
    </body>
</html>