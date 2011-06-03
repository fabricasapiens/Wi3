<?php

    return 
    Array
    (
        "version" => "1.0",
        "templates" => Array
        (
            "StrakFluweel" => Array
            (
                "title" => "Strak fluweel",
                "description" => "Strak, maar met een zachte uitstraling",
                "path" => Wi3::inst()->pathof->app ."templates/strakfluweel/",
                "url" => Wi3::inst()->urlof->appfiles ."templates/strakfluweel/"
            ),
            "fabricasapiens.nl" => Array
            (
                "title" => "fabricasapiens.nl",
                "description" => "De enige echte",
                "path" => Wi3::inst()->pathof->app ."templates/fabricasapiens.nl/",
                "url" => Wi3::inst()->urlof->appfiles ."templates/fabricasapiens.nl/"
            ),
            "wi3.nl" => Array
            (
                "title" => "wi3.nl",
                "description" => "Template for wi3.nl",
                "path" => Wi3::inst()->pathof->app ."templates/wi3.nl/",
                "url" => Wi3::inst()->urlof->appfiles ."templates/wi3.nl/"
            )
        )
    );
    
?>
