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
    </head>
    <body>
    
        <div id='topshadow'>
        </div>
        <div id='lefttopcorner'>
        </div>
        <div id='righttopcorner'>
        </div>
    
        <div id='wrap'>
            <div id='column'>
                <div id='top'>
                    <div id='navigation'>
                        <?php
                        
                            echo Wi3::inst()->sitearea->navigation->menu->render();
                            
                        ?>
                    </div>
                </div>
                <div id='middle'>
                    <div id='background'>
                    </div>
                    <div id='content'>
                    <?php
                    
                        // Get url to the template base-url
                        $baseurl = substr($this->_params["javascript_url"], 0, strpos($this->_params["javascript_url"], "/static/javascript")) . "/";
                        
                        try 
                        {
                            include("subviews/".strtolower($page->longtitle).".php");
                        }
                        catch(Exception $e)
                        {
							// Default editable block
                            echo "<cms type='editableblock' name='content'><p>Dit is wijzigbare tekst</p></cms>";
                        }
                    
                    ?>
                    </div>
                </div>
                <div id='bottom'>
                    <div style='float: left; margin: 20px;'>
                        <p>Adres</p>
                        <p>Brederodehof 63<br />
                        3341 VC H-I-Ambacht</p>
                    </div>
                    <div style='float: left; margin: 20px;'>
                        <p>Contact</p>
                        <p>info@fabricasapiens.nl</p>
                    </div>
                    <div style='float: left; margin: 20px;'>
                        <p>Bankgegevens</p>
                        <p>Postbank 8363603</p>
                    </div>       
                    <div style='float: left; margin: 20px;'>
                        <p>Formele gegevens</p>
                        <p>
                         KvK-nr. 81573810<br />
                         BTW-nr. NL101269043B01</p>
                    </div>                    
                </div>
            </div>
        </div>
        
          <script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
        document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
        </script>
        <script type="text/javascript">
        try {
        var pageTracker = _gat._getTracker("UA-258109-5");
        pageTracker._trackPageview();
        } catch(err) {}</script>

        
    </body>
</html>
