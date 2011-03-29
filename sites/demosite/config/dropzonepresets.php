<?php

    return 
    Array
    (
        "version" => "1.0",
        "dropzonepresets" => Array
        (
            "Text" => Array
            (
                "title" => "Tekstpagina",
                "description" => "Pagina met tekst",
                "path" => Wi3::inst()->pathof->site . "/dropzonepresets/text.php"
            ),
            "Gallery" => Array
            (
                "title" => "Afbeeldingsgalerij",
                "description" => "Pagina met afbeeldingsgalerij",
                "path" => Wi3::inst()->pathof->site . "/dropzonepresets/gallery.php"
            )
        )
    );

    
    
?>
