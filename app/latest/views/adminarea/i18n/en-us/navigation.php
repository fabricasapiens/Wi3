<ul>
    <?php
    
    $menu = Array(
        "menu" => "Menu",
        "content" => "Content",
        "files" => "Files"
    );
    
    //enable user-management if the user is allowed to do so
    if (Wi3::inst()->acl->check(new Controller_Adminarea(Request::instance()), NULL, TRUE) === TRUE) {
        $menu["engine/users"] = "Users";
    }
    
    //disable filemanagement if it is disabled for this user
    /*
    if ($site->filemanagement == "no") {
        unset($menu["engine/files"]);
    }*/
    
    foreach($menu as $action => $urltext) {
        echo "<li" .  ( (Wi3::inst()->routing->action == $action) ? " class='active'" : "") . ">" .  html::anchor(Wi3::inst()->urlof->action($action), $urltext) . "</li>";
    }

    ?>

</ul>
