<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Formbuilder Folderselector class
 * @package Wi3
 * @author	Willem Mulder
 */
 
class Wi3_Formbuilder_Folderselector extends Wi3_Formbuilder_Base
{
   
    public function addextension($name)
    {
        if (!isset($this->settings->fileextensions) OR !is_array($this->settings->fileextensions))
        {
            $this->settings->fileextensions = Array($name => $name);
        }
        else
        {
            $this->settings->fileextensions[$name] = $name;
        }
        return $this;
    }
    
    public function removeextension($name)
    {
        if (isset($this->settings->fileextensions))
        {
            unset($this->settings->fileextensions[$name]);
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
        
            $folders = Wi3::inst()->sitearea->files->findfolders(array("fileextensions" => $this->settings->fileextensions));
            
            $counter = 0;
            foreach($folders as $folder) {
                $counter++;
                echo "<div style='float: left; background: #aaa; ";
                if (isset($this->settings->selected) AND $folder->id != $this->settings->selected)
                {
                    echo "opacity: 0.4; ";
                }
                echo "margin: 5px;' id='image_".$id."_".$counter."' class='image_".$id."'>";
                    echo "<a href='javascript:void(0)' style='text-decoration: none;' onClick='$(\"#input_".$id."\").val(\"".$folder->id."\").has(\"xyz\").add(\"#image_".$id."_".$counter."\").css(\"background\", \"#1891FF\").fadeTo(50,1).has(\"xyz\").add(\".image_".$id."\").not(\"#image_".$id."_".$counter."\").css(\"background\", \"#eee\").fadeTo(50,0.40);'>";
                    echo "<div style='float: left; margin: 5px; height: 20px; overflow: hidden;'>" .$folder->filename . "</div></a>";
                echo "</div>";
            }

        echo "<div style='font-size: 1px; visibility: hidden; clear:both;'>.</div>";
        echo "</div>";
    }
   
}
    
?>
