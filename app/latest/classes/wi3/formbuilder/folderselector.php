<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Formbuilder Fileselector class
 * @package Wi3
 * @author	Willem Mulder
 */
 
class Wi3_Formbuilder_Fileselector extends Wi3_Formbuilder_Base
{
   
    public function addextension($name)
    {
        if (!isset($this->settings->extensions) OR !is_array($this->settings->extensions))
        {
            $this->settings->extensions = Array($name => $name);
        }
        else
        {
            $this->settings->extensions[$name] = $name;
        }
        return $this;
    }
    
    public function removeextension($name)
    {
        if (isset($this->settings->extensions))
        {
            unset($this->settings->extensions[$name]);
        }
        return $this;
    }
    
    public function render()
    {
        
        $id = Wi3::date_now();
    
        if (isset($this->attributes->label))
        {
            echo "<label for='" . $this->attributes->name . "'></label>";
        }
        echo "<input type='hidden' name='" . $this->attributes->name . "' id='input_" . $id . "' />";
        echo "<div style='padding: 10px;'>";
        
            $images = Wi3::inst()->sitearea->files->find(array("extensions" => $this->settings->extensions));
            
            $counter = 0;
            foreach($images as $image) {
                $counter++;
                echo "<div style='float: left; background: #eee; ";
                if (isset($this->settings->selected) AND $image->id != $this->settings->selected)
                {
                    echo "opacity: 0.4; ";
                }
                echo "margin: 5px;' id='image_".$id."_".$counter."' class='image_".$id."'>";
                    echo "<a href='javascript:void(0)' style='text-decoration: none;' onClick='$(\"#input_".$id."\").val(\"".$image->id."\").has(\"xyz\").add(\"#image_".$id."_".$counter."\").fadeTo(50,1).has(\"xyz\").add(\".image_".$id."\").not(\"#image_".$id."_".$counter."\").fadeTo(50,0.40);'>";
                    echo "<div style='float: left; margin: 5px;'><img src='" . Wi3::inst()->urlof->sitefiles . "data/uploads/50/" . $image->filename . "'></img></div></a>";
                echo "</div>";
            }

        echo "<div style='font-size: 1px; visibility: hidden; clear:both;'>.</div>";
        echo "</div>";
    }
   
}
    
?>
