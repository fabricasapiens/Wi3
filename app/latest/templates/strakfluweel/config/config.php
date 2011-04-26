<?php

    return 
    Array
    (
        "version" => "1.0",
        "templateview" => Wi3::inst()->pathof->app."templates/strakfluweel/template.php",
        "dropzones" => Array("main"),
        "preview" => Array(
            "imageurl" => Wi3::inst()->urlof->appfiles."templates/strakfluweel/static/images/template_preview.png",
            "dropzones" => Array
            (
                "main" => Array 
                (
                    "position" => Array( "top" => 30, "left" => 50, "width" => 150, "height" => 90),  // Note: this is the position of the dropzone in the preview image!!
                    "textcolor" => "#000" // This is the color of the text that will appear in the dropzone preview div. Black for maximum contrast
                )
            )
        )
    );
    
?>
