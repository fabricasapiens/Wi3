<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html>

<?php
    $page = Wi3::inst()->sitearea->page;
    $site = Wi3::inst()->sitearea->site;

    // First, load the CSS
    $this->css("reset.css");
    $this->css("style.css");

    // Load JQuery UI (also loads Jquery Core via dependencies)
    Wi3::inst()->plugins->load("plugin_jquery_ui");

?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Wi3.nl - <?php echo $page->longtitle; ?>
    </head>
    <body>
    
        <div id='container'>
            <div id='navigationandstatus'>
                <div id='wi3logo'></div>
                <div id='navigation'>
                    <?php
                        
                        echo Wi3::inst()->sitearea->navigation->menu->render();
                        
                    ?>
                </div>
            </div>
            <div id='content'>
                <?php
                    
                    // Get url to the template base-url
                    $baseurl = substr($this->_params["javascript_url"], 0, strpos($this->_params["javascript_url"], "/static/javascript")) . "/";
                    
                    try 
                    {
                        // Add view, and make it inherit the current view (i.e. $this)
                        echo $this->view(strtolower($page->longtitle))->set("this", $this)->set("page", $page)->set("site",$site)->render();
                    }
                    catch(Exception $e)
                    {
                        // Add an editable part
                        echo "<cms type='editableblock' name='group1'>
                        </cms>";
                    }
                
                ?>
            </div>
        </div>
        <div id='footer'>
            Copyright 2010-2011 <a href='http://fabricasapiens.nl'>Fabrica Sapiens</a>. All rights reserved. Source code on <a href='https://github.com/fabricasapiens/Wi3'>Github</a>
        </div>
        
          <script type="text/javascript">

          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-258109-11']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

        </script>

    </body>
</html>
