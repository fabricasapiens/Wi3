<?php 

    // Include the components in the modules() list
    // Wi3 is not yet loaded, so we can't use the pathof function
    $componentsdir = Wi3::inst()->unixpath(__DIR__)."/components/";
    $it = new DirectoryIterator($componentsdir);
    $components = Array();
    foreach ( $it as $file ) {
        if ($file->isDir() AND !$file->isDot())
        {
            $components["component_".$file->getBasename()] = $componentsdir . $file->getBasename();           
        }
    }
    Kohana::modules((Kohana::modules() + $components));

?>
