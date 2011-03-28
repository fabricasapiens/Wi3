<?php

    //chdir("/var/www/vhosts/127.0.0.1/");

    // Try to get somewhere in the file tree
    if ($handle = opendir('.')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            echo $file . "<br />";
        }
    }
    closedir($handle);
}

?>