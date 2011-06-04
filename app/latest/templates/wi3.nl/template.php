<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

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
                        echo $this->view(strtolower($page->longtitle))->set("page", $page)->set("site",$site)->render();
                    }
                    catch(Exception $e)
                    {
                        // Do nothing
                    }
                
                ?>
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
