<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Formbuilder Fileselector class
 * @package Wi3
 * @author	Willem Mulder
 */

class Wi3_Formbuilder_Fileselector extends Wi3_HTML_FormElement
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
        $val = $this->val();

        if (isset($this->attributes->label))
        {
            echo "<label for='" . $this->attributes->name . "'></label>";
        }
        echo "<input type='hidden' name='" . $this->attributes->name . "' id='input_" . $id . "' value='" . $val . "' />";
        echo "<div style='padding: 10px;'>";

		if (isset($this->settings->extensions)) {
			$files = Wi3::inst()->sitearea->files->find(array("extensions" => $this->settings->extensions));
		} else {
			$files = Wi3::inst()->sitearea->files->find(array());
		}

        $counter = 0;
        foreach($files as $file) {
        	$level = $file->{$file->level_column};
            $counter++;
            echo "<div style='padding-left: " . ($level * 10) . "px; ";
            if ($file->id != $val)
            {
                echo "opacity: 0.6; ";
            }
            echo "margin: 5px;' id='file_".$id."_".$counter."' class='file_".$id."'>";
                echo "<a href='javascript:void(0)' style='text-decoration: none;' onClick='$(\"#input_".$id."\").val(\"".$file->id."\").has(\"xyz\").add(\"#file_".$id."_".$counter."\").fadeTo(50,1).has(\"xyz\").add(\".file_".$id."\").not(\"#file_".$id."_".$counter."\").fadeTo(50,0.40);'>";
                	echo "<div style='margin: 5px;'>";
	                	// Add small thumbnail if file is an image
	                	if ($file->isImage()) {
	                		echo "<img src='" . Wi3::inst()->urlof->site . "_uploads/30/" . $file->filename . "'/> ";
	                	}
	                	echo $file->filename;
                	echo "</div>";
                echo "</a>";
            echo "</div>";
        }

        echo "<div style='font-size: 1px; visibility: hidden; clear:both;'>.</div>";
        echo "</div>";
    }

}

?>
