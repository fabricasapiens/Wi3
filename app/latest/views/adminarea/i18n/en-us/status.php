<?php

    //the wi3 ajax request indicator
    echo "<div id='wi3_ajax_menu'>
        <div id='wi3_ajax_indicator'>0</div>
        <div id='wi3_notification_bottom'>notificatio_bottom</div>
        <div id='wi3_notification_top'>notification_top</div>
    </div>";

    if (isset(Wi3::inst()->sitearea->auth->user)) {
        echo "Logged in as <strong>" . Wi3::inst()->sitearea->auth->user->username . "</strong>. [" . html::anchor(Wi3::inst()->urlof->action("logout"), "log out") . "]";
    } else {
        echo "Not logged in.";
    }

?>
