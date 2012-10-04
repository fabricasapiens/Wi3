<?php

    // field is given

    // If pqfield (the <cms> representation) is given, get the style options (e.g. float property and the margin property)
    if (isset($pqfield))
    {
        // Get set style
        $style = pq($pqfield)->attr("style");
        $field->options["style"] = $style;
        // Get style options
        $stylearray = Array();
        $stylearray["float"] = pq($pqfield)->attr("style_float");
        $stylearray["padding"] = pq($pqfield)->attr("style_padding");
        $stylearray["width"] = pq($pqfield)->attr("style_width");
        // Only set an explicit display block if no display is found in '$style'
        if (strpos($style, "display:") === false) {
             $stylearray["display"] = "block";
        }
        $field->options["stylearray"] = $stylearray;
    }
    else
    {
        // Default style options
        $stylearray = Array();
        $stylearray["float"] = "left";
        $stylearray["padding"] = "20px";
        $stylearray["width"] = "";
        $stylearray["display"] = "block"; // default, can be overridden by the field during its rendering process
        $field->options["stylearray"] = $stylearray;
    }
    
    // Render the field, in which the field can also change the style options
    $fieldhtml = $field->render();
    // The field can override these options, if it wants
    $style = $field->options["style"];
    $stylearray = $field->options["stylearray"];
    // Once the field is rendered, it is known whether it wants to be an inline element, or a block element
    // Use float and padding only if element is not inline
    if (strpos($style, "display:inline") !== false || 
        (isset($stylearray["display"]) && $stylearray["display"] == "inline"))
    {
        unset($stylearray["float"]);
        unset($stylearray["padding"]);
    }
    // Calculate total style
    $totalstyle = $style;
    foreach($stylearray as $name => $val) {
        if (!empty($val)) {
            $totalstyle .= "; " . $name . ":" . $val;
        }
    }
    $totalstyle .= "; position: relative;";
    
    echo "<span type='field' fieldid='" . $field->id . "' style='" . $totalstyle . "' contenteditable='false'>";
        echo "<span type='fieldbuttons' style='position: absolute; z-index: 1000; left: 0px; top: 0px; height: 80px; margin-top: -80px; width: 100%; min-width: 250px; display: none; font: 13px arial, verdana; font-weight: normal; background: #fff; overflow: hidden; -webkit-box-shadow: 0px 0px 10px #ccc; -mozilla-box-shadow: 0px 0px 10px #ccc; box-shadow: 0px 0px 10px #ccc;'><span style='display: inline-block; padding: 10px;'>";
            // Now there is three tabs: at the left the field-actions. At the right respectively the style-actions, and rigid actions like 'delete' or 'replace with other field'
            ?>
            <span type='field_tabs_tabs' style='position: absolute; bottom: 0px; height: 20px;'>
                <span tab='fieldactions' style='margin-right: 5px;'><a href='javascript:void(0);' onclick='$("span[block]").hide().filter("span[block=fieldactions]").fadeIn();'>veld acties...</a></span>
                <?php 
                if (isset($stylearray["display"] && $stylearray["display"] == "block") 
                { ?>
                    <span tab='design' style='margin-right: 5px;'><a href='javascript:void(0);' onclick='$("span[block]").hide().filter("span[block=design]").fadeIn();'>opmaak...</a></span>
                <?php } ?>
                <span tab='settings' style='margin-right: 5px;'><a href='javascript:void(0);' onclick='$("span[block]").hide().filter("span[block=settings]").fadeIn();'>verwijderen...</a></span>
            </span>
            <span type='field_tabs_blocks' style='position:absolute; bottom: 20px; height: 50px; overflow: hidden;'>
                <span block='fieldactions'>
                    <?php echo $field->fieldactions(); ?>
                </span>
                <span block='design' style='display: none;'>
                    <?php 
                    // Padding of the field object
                    echo "<span><span style='display: inline-block; width: 60px;'>Marge </span><a href='javascript:void(0)' action='margin_0px'>geen</a> <a href='javascript:void(0)' action='margin_20px'>gemiddeld</a> <a href='javascript:void(0)' action='margin_40px'>groot</a></span><br />";
                    // Float left or right
                    // <img alt='veld links plaatsen' action='float_left' style='border: none; text-decoration: none;  vertical-align:middle;' src='" . Wi3::inst()->urlof->pagefillerfiles . "static/images/edittoolbar/float_left.png" . "'/><img alt='veld rechts plaatsen' action='float_right' style='border: none; text-decoration: none;  vertical-align:middle;' src='" . Wi3::inst()->urlof->pagefillerfiles . "static/images/edittoolbar/float_right.png" . "'/>
                    echo "<span><span style='display: inline-block; width: 60px;'>Uitlijning</span><a href='javascript:void(0)' action='align_floatleft'>links</a> <a href='javascript:void(0)' action='align_floatright'>rechts</a> <a href='javascript:void(0)' action='align_fullwidth'>volle breedte</a></span><br />";
                echo "</span>";
                echo "<span block='settings' style='display: none;'>
                    <img alt='veld verwijderen' action='remove' style='border: none; text-decoration: none;  vertical-align:middle;' src='" . Wi3::inst()->urlof->pagefillerfiles . "static/images/edittoolbar/remove.png" . "'/>";
                    ?>
                </span>
            </span>
        <?php 
        echo "</span></span>"; // PHPQuery 'markups' the html so that a stupid line-break / space (?) gets inserted here, mangling the markup of the page...
        echo "<span style='display: inherit;' type='fieldcontent'>" . $fieldhtml . "</span>";
    echo "</span>";
    
?>
