<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Formbuilder Dateselector class
 * @package Wi3
 * @author	Willem Mulder
 */
 
class Wi3_Formbuilder_Dateselector extends Wi3_Formbuilder_Base
{
   
    public function render()
    {
    
        $id = Wi3::inst()->date_now();
    
        $ret = "<label for='" . $this->attributes->name . "'/><input id='input_" . $id . "' name='" . $this->attributes->name . "' ";
        if (isset($this->attributes->value)) 
        {
            $ret .= "value='" . $this->attributes->value . "' ";
        }
        $ret .= "></input>";
        
        $ret .= '
        <script>
        //datePickers on input with class componentInput_datepicker
        $(document).ready(function() {
            $.datepicker.setDefaults({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true });
                //"#"+prefix+"_add input.componentInput_datepicker, #"+prefix+"_edit input.componentInput_datepicker"
                $("#input_' . $id . '").each(function() {
                //give ID if none is attached yet
                //if ($(this).attr("id").length < 1) {
                //    counter++;
                //    $(this).attr("id", "input_" + wi3.dateNow() + counter);
                //}
                $(this).datepicker();
            });
        });
        </script>
        ';
        
        return $ret;
    }
   
}
    
?>
