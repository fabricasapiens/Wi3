<html>

    <head>

        <?php
        
            $this->css("reset.css");
            $this->css("style.css");
        
        ?>
        
    </head>
    <body>
    
        <div id='nav'>
            <?php
            
                echo Wi3::inst()->sitearea->navigation->menu->render();
            
            ?>
        </div>
    
        <div id='content'>
    
            <h1>Non editable title of <?php echo Wi3::inst()->sitearea->page->longtitle; ?></h1>
            <cms type='editableblock' name='content'><h2>Default subtitle for page <?php echo  Wi3::inst()->sitearea->page->longtitle; ?></h2><p>This is some default content.</p></cms>
            
        </div>
    
    </body>

</html>
