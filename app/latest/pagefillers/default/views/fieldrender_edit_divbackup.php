<?php

    // field is given

    // If pqfield (the <cms> representation) is given, get the style options (e.g. float property and the margin property)
    if (isset($pqfield))
    {
        // Get style options
        $style = Array();
        $style["float"] = pq($pqfield)->attr("style_float");
        $style["padding"] = pq($pqfield)->attr("style_padding");
        $style["display"] = "block"; // default, can be overridden by the field during its rendering process
        $field->options["style"] = $style;
    }
    else
    {
        // Default style options
        $style = Array();
        $style["float"] = "left";
        $style["padding"] = "20px";
        $style["display"] = "block"; // default, can be overridden by the field during its rendering process
        $field->options["style"] = $style;
    }
    
    // Get a render of the field. The field can change style options during its rendering process...
    $fieldhtml = $field->render();
    $style = $field->options["style"];
    
    // Replace the <cms> part with a render of the field
    if ($style["display"] == "block")
    {
        // Block element, use float and padding
        echo "<span type='field' fieldid='" . $field->id . "' style='float: " . $style["float"] . "; display: block; padding: " . $style["padding"] . "; position: relative;' contenteditable='false'>";
        $fieldcontentstyle = "style='display: block;' ";
    }
    else
    {
        // Inline element, do not use the float and padding property
        echo "<span type='field' fieldid='" . $field->id . "' style='display: inline; position: relative;' contenteditable='false'>\r\n";
        $fieldcontentstyle = "style='display: inline;' ";
    }
        echo "<div type='fieldbuttons' style='position: absolute; left: 0px; top: 0px; height: 80px; margin-top: -80px; width: 100%; min-width: 250px; display: none; font: 13px arial, verdana; font-weight: normal; background: #fff; overflow: hidden; -webkit-box-shadow: 0px 0px 10px #ccc; -mozilla-box-shadow: 0px 0px 10px #ccc; box-shadow: 0px 0px 10px #ccc;'><div style='padding: 10px;'>";
            // Now there is three tabs: at the left the field-actions. At the right respectively the style-actions, and rigid actions like 'delete' or 'replace with other field'
            ?>
            <div type='field_tabs_tabs' style='position: absolute; bottom: 0px; height: 20px;'>
                <span tab='fieldactions' style='margin-right: 5px;'><a href='javascript:void(0);' onclick='$("div[block]").hide().filter("div[block=fieldactions]").fadeIn();'>veld acties...</a></span>
                <?php 
                if ($style["display"] == "block") 
                { ?>
                    <span tab='design' style='margin-right: 5px;'><a href='javascript:void(0);' onclick='$("div[block]").hide().filter("div[block=design]").fadeIn();'>opmaak...</a></span>
                <?php } ?>
                <span tab='settings' style='margin-right: 5px;'><a href='javascript:void(0);' onclick='$("div[block]").hide().filter("div[block=settings]").fadeIn();'>verwijderen...</a></span>
            </div>
            <div type='field_tabs_blocks' style='position:absolute; bottom: 20px; height: 50px; overflow: hidden;'>
                <div block='fieldactions'>
                    <?php echo $field->fieldactions(); ?>
                </div>
                <div block='design' style='display: none;'>
                    <?php 
                    // Padding of the field object
                    echo "<div><span style='display: inline-block; width: 60px;'>Marge </span><a href='javascript:void(0)' action='margin_0px'>geen</a> <a href='javascript:void(0)' action='margin_20px'>gemiddeld</a> <a href='javascript:void(0)' action='margin_40px'>groot</a></div>";
                    // Float left or right
                    // <img alt='veld links plaatsen' action='float_left' style='border: none; text-decoration: none;  vertical-align:middle;' src='" . Wi3::inst()->urlof->pagefillerfiles . "static/images/edittoolbar/float_left.png" . "'/><img alt='veld rechts plaatsen' action='float_right' style='border: none; text-decoration: none;  vertical-align:middle;' src='" . Wi3::inst()->urlof->pagefillerfiles . "static/images/edittoolbar/float_right.png" . "'/>
                    echo "<div><span style='display: inline-block; width: 60px;'>Uitlijning</span><a href='javascript:void(0)' action='float_left'>links</a> <a href='javascript:void(0)' action='float_right'>rechts</a></div>
                </div>
                <div block='settings' style='display: none;'>
                    <img alt='veld verwijderen' action='remove' style='border: none; text-decoration: none;  vertical-align:middle;' src='" . Wi3::inst()->urlof->pagefillerfiles . "static/images/edittoolbar/remove.png" . "'/>";
                    ?>
                </div>
            </div>
        <?php 
        echo "</div></div>"; // PHPQuery 'markups' the html so that a stupid line-break / space (?) gets inserted here, mangling the markup of the page...
        echo "<div " . $fieldcontentstyle . " type='fieldcontent'>" . $fieldhtml . "</div>";
    echo "</span>";
    
?>
