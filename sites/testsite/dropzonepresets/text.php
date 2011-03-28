<?php

    return 
    Array(
        "version" => "1.0",
        "dropzonepreset" => Array
        (
            "dropzones" => Array
            (
                "main" => Array
                (
                    "defaultfields" => Array
                    (   
                        //these fields will be created by default when a new page is created
                        "component_text" => Array
                        (
                            "fieldtype" => "component_text",
                            "title" => "tekst",
                            "config" => Array()
                        ),
                    ) 
                ),
                "secondary" => Array
                (
                    "defaultfields" => Array("component_text") //these fields will be created by default when a new page is created
                )
            )
        )
    );
    
?>