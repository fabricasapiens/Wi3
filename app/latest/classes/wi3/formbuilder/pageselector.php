<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Formbuilder Pageselector class
 * @package Wi3
 * @author	Willem Mulder
 */

class Wi3_Formbuilder_Pageselector extends Wi3_HTML_FormElement
{

	private $activepage = NULL;
	private $pagePositions = NULL; // Should be a list of pagePositions
	private $pages = NULL;

	public function setactivepage($page)
    {
        $this->activepage = $page;
        return $this;
    }

	public function pagePositions($list = null) {
		if ($list != null) {
			$this->pagePositions = $list;
			return $this;
		}
		return $this->pagePositions;
	}

	public function loadPages() {
        if (!isset($this->pages)) {
            $pages = Array();
            if ($this->pagePositions() == null) {
                $this->pagePositions(Wi3::inst()->sitearea->pagepositions->getall());
            }
            $pagepositions = $this->pagePositions();
            foreach($pagepositions as $pageposition)
            {
                $pagepositionpages = $pageposition->pages;
                $page = $pagepositionpages[0]; // Simply get first page
                $pages[] = $page;
            }
            $this->pages = $pages;
        }
        return $this->pages;
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

        	$this->loadPages();
			$pagePositions = $this->pagePositions();

            $counter = 0;
            foreach($pagePositions as $pagePosition)
            {
                $page = $pagePosition->pages[0];
                $level = $pagePosition->{$pagePosition->level_column};
                $counter++;
                echo "<div style='padding-left: " . ($level * 10) . "px; ";
                if ($pagePosition->id != $val)
                {
                    echo "opacity: 0.6; ";
                }
                echo "margin: 5px;' id='pagePosition_".$id."_".$counter."' class='pagePosition_".$id."'>";
                    echo "<a href='javascript:void(0)' style='text-decoration: none;' onClick='$(\"#input_".$id."\").val(\"".$pagePosition->id."\").has(\"xyz\").add(\"#pagePosition_".$id."_".$counter."\").fadeTo(50,1).has(\"xyz\").add(\".image_".$id."\").not(\"#pagePosition_".$id."_".$counter."\").fadeTo(50,0.40);'>";
                    echo $page->longtitle . "</a>";
                echo "</div>";
            }
        echo "</div>";
    }

}

?>
