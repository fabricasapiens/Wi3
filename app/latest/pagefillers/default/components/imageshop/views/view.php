<div class='component_imageshop relativepositioning'>
    <?php

        $this->css("style.css");
        $this->javascript("imageshop.js");
        
        //load the central JQuery plugin
        Wi3::inst()->plugins->load("plugin_jquery_core");

        $leftbar = "<div class='mediumpadding leftbar mediumtransparentbackground paddingmarginwithinbox'>";
        foreach($files as $file) {
            $leftbar .= thumbnail($file);
        }
        $leftbar .= "</div>";
        echo $leftbar;
        
        echo "<button class='orderbutton paddingmarginwithinbox'>Bestel</button>";

        $rightbar = "<div class='mediumpadding rightbar mediumtransparentbackground paddingmarginwithinbox'>";
        $rightbar .= "<div class='largeimage'></div>";
        $rightbar .= "<div class='cart'>Klik op de <button>+</button> onder een product om het toe te voegen aan de winkelmand.</div>";
//<button data-buttontype='increase'>+</button><button data-buttontype='decrease'>-</button><span class='smallpadding' data-amount='0'>0</span>
        $rightbar .= "</div>";
        echo $rightbar;

        echo "<div class='clearfix'>.</div>";
        
        function thumbnail($file) {
            $ret = "";
            $ret .= "<div class='smallmargin thumbnail hoverable smallpadding'>";
                $ret .= "<div class='imagecontainer'><img src='" . Wi3::inst()->urlof->sitefiles . "data/uploads/50/" . $file->filename . "' fullSRC='" . Wi3::inst()->urlof->sitefiles . "data/uploads/" . $file->filename . "'></img></div>";
                $ret .= "<div class='productbuttons'><button class='smallpadding paddingmarginwithinbox' data-buttontype='addtocart' data-productid='" . $file->id .  "'>+</button></div>";
            $ret .= "</div>";
            return $ret;
        }

    ?>
</div>