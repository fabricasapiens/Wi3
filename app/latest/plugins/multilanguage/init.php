<?php

    // Create a callback so that the multilanguage pageversion-class will be loaded once Wi3::inst()->sitearea->pages asks all pageversion-classes to do so
    Event::instance('wi3.sitearea.pages.versionplugins.load')->callback(array("Wi3_plugins", "load"), "plugin_multilanguage"); 

?>
