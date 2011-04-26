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
        <div id='navigationandstatus'>
            <div id='wi3logo'></div>
            <div id='navigation'>
                <ul>
                    <li class='active'><a href='<?php echo Wi3::inst()->urlof->controller("superadminarea"); ?>'>Sites</a></li>
                </ul>
            </div>
            <div id='status'>
                <?php echo "Ingelogd als <strong>" . Wi3::inst()->globalauth->user->username . "</strong>. [" . html::anchor(Wi3::inst()->urlof->action("logout"), "uitloggen") . "]"; ?>
            </div>
        </div>
        <div id='content'>
            <?php if (isset($content)) { echo $content; } else { echo View::factory("superadminarea/dashboard"); } ?>
        </div>
    </div>

</body>
</html>
