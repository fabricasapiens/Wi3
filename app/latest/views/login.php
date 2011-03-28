<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php if (isset($title)) { echo html::specialchars($title); } ?></title>
    
    <?php
    
        $this->css("reset.css");
        $this->css("style.css");
    
    ?>

</head>
<body>

    <div id='container'>
        <div id='navigation'>
            <div id='navigationleft'></div>
            <?php if (isset($navigationright)) { echo "<div id='navigationright'>" .$navigationright."</div>"; } ?>
        </div>
    </div>

    <div id='loginform'>
        <div id='loginform_content'>
            <?php 
                //if (isset($title)) { echo "<h1>" . html::specialchars($title) . "</h1>"; } 
            ?>
            <?php if (isset($content)) { echo $content; } ?>
           
            <p id="loginform_copyright">
                Copyright ©2007–2010 Fabrica Sapiens
            </p>
        </div>
    </div>

</body>
</html>