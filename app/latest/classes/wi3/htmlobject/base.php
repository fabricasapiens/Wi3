<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Htmlobject Base class
 * @package Wi3
 * @author	Willem Mulder
 */
 
class Wi3_Htmlobject_Base extends Wi3_Base
{

    public $settings;
    public $attributes;
    public $tag;
    public $content;
    
    function __construct()
    {
       $this->settings = $this->attributes = new ArrayObject(Array(), 2); // The 2 flag to enable standard object->something reading and writing
    }
   
    // All these functions are chainable
    public function set($setting, $val)
    {
        $this->settings->$setting = $val;
        return $this;
    }
    
    public function __SET($setting, $val)
    {
        $this->set($setting, $val);
        return $this;
    }
    
    public function tag($tag) {
        $this->tag = $tag;
        return $this;
    }
    
    public function content($content) {
        $this->content = $content;
        return $this;
    }
    
    public function attr($attr, $val = null)
    {
        if (is_array($attr)) {
            foreach($attr as $key => $val) {
                $this->attr($key, $val);
            }
        } else {
            if (empty($val))
            {
                if (isset($this->attributes->{$attr})) {
                    unset($this->attributes->{$attr});
                }
            }
            else
            {
            $this->attributes->{$attr} = $val;
            }
        }
        return $this;
    }
    
    public function renderOpenTag() {
        $attrhtml = "";
        foreach($this->attributes as $name => $value) {
            $attrhtml .= " " . $name . "='" . $value . "' ";
        }
        return "<" . $this->tag . $attrhtml . ">";
    }
    
    public function renderContent() {
        return $this-content;
    }
    
    public function renderCloseTag() {
        return "</" . $this->tag . ">";
    }
    
    public function render() {
        return $this->renderOpenTag() . $this->renderContent() . $this->renderCloseTag();
    }
    
} // End class
    
?>
