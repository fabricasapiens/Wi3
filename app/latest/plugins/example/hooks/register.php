<?php

    //this registers this plugin with Wi3::$plugins
    //it is not obligatory, but ensures the plugin is created, so the plugin can execute some actions and stuff 
    Event::add("wi3.registerplugins", array("Plugin_example", "register"));

?>