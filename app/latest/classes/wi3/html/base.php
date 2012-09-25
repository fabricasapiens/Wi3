<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Htmlobject Base class
 * @author	Willem Mulder
 */
 
class Wi3_Html_Base extends Wi3_Base
{

	// Arbitrary settings that the component can use
    public $settings;
	
	// Tagname and attributes of the open and close tag
    public $tagname;
	public $attributes;
	
	// Callbacks
	public $callbacks;
    
    public $content;
	
	// Children elements
	// Children can be added with add(object) and remove(object)
	public $children;
    
    function __construct()
    {
       $this->settings = new ArrayObject(Array(), 2); // The 2 flag to enable standard object->something reading and writing
	   $this->attributes = new ArrayObject(Array(), 2);
	   $this->children = new ArrayObject(Array(), 2);
	   $this->callbacks = new ArrayObject(Array(), 2);
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
    
    public function tagname($tagname) {
        $this->tagname = $tagname;
        return $this;
    }
    
    public function content($content=null) {
        if ($content === null) {
			return $this->content;
		} else {
			$this->content = $content;
			return $this;
		}
    }
	
	public function html($html) {
        return $this->content($html);
    }
    
    public function attr($attr, $val = null)
    {
        if (is_array($attr)) {
            foreach($attr as $key => $val) {
                $this->attr($key, $val);
            }
        } else {
			if ($val === null) {
				return isset($this->attributes->{$attr}) ? $this->attributes->{$attr} : "";
			} else if (empty($val) && $val !== "0" && $val !== 0) {
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
	
	public function add($child) {
		$this->children[] = $child;
	}
	
	public function remove($child) {
		foreach($this->children as $key => $val) {
			if($val !== $child) {
				unset($this->children[$key]);
			}
		}
	}	
	
	public function findChildByProperty($key, $value) {
		foreach($this->children as $id => $child) {
			if (isset($child->{$key}) && $child->{$key} === $value) {
				return $child;
			}
		}
		return false;
	}
	
	public function findChildByAttribute($key, $value) {
		foreach($this->children as $id => $child) {
			if ($child->attr($key) === $value) {
				return $child;
			}
		}
		return false;
	}
	
	public function renderBeforeTag() {
		return "";
	}
    
    public function renderOpenTag() {
        $attrhtml = "";
        foreach($this->attributes as $name => $value) {
            $attrhtml .= " " . $name . "='" . $value . "' ";
        }
        return "<" . $this->tagname . $attrhtml . ">";
    }
    
    public function renderContent() {
        // Return arbitrary content, if set
		if ($this->content != null) {
			return $this->content;
		} else {
			// Render children
			$content = "";
			foreach($this->children as $child) {
				if ($child instanceof Html_Base) {
					$content .= $child->render();
				}
			}
			return $content;
		}
    }
    
    public function renderCloseTag() {
        return "</" . $this->tagname . ">";
    }
	
	public function renderAfterTag() {
		return "";
	}
	
	public function onBeforeRender() {
		
	}
	
	public function onAfterRender() {
		
	}
    
    public function render() {
		$this->onBeforeRender();
        $return = $this->renderBeforeTag() . $this->renderOpenTag() . $this->renderContent() . $this->renderCloseTag() . $this->renderAfterTag();
		$this->onAfterRender();
		return $return;
    }
	
	public function __toString() {
		return $this->render();
	}
	
	public function on($name, $data, $callback = null) {
		if ($callback == null) {
			$callback = $data;
			$data = null;
		}
		if (!isset($this->callbacks[$name]) || is_array($this->callbacks[$name])) {
			$this->callbacks[$name] = Array();
		}
		$this->callbacks[$name][] = Array("function" => $callback, "data" => $data);
	}
	
	public function trigger($name) {
		if (!isset($this->callbacks[$name]) || !is_array($this->callbacks[$name])) {
			$this->callbacks[$name] = Array();
		}
		foreach($this->callbacks[$name] as $callback) {
			// Execute function
			$callback["function"]($callback["data"]);
		}
	}
	
	public function addClass($className) {
		$classes = $this->attr("class").split(" ");
		$found = false;
		foreach($classes as $index => $c) {
			if (strtolower($c) === strtolower($className)) {
				$found = true;
				break;
			}
		}
		if (!$found) {
			$classes[] = $className;
		}
		$this->attr("class", $classes.join(" "));
	}
	
	public function removeClass($className) {
		$classes = $this->attr("class").split(" ");
		foreach($classes as $index => $c) {
			if (strtolower($c) === strtolower($className)) {
				unset($classes[$index]);
				break;
			}
		}
		$this->attr("class", $classes.join(" "));
	}
    
} // End class

/*
        public static function optionlist($list, $selectedval) {
            $ret = "";
            //watch out, (string)false == (string)"" !!!
            foreach($list as $val => $label) {
                $ret .= "<option value='" . $val . "' " . ((strlen($selectedval)>0 AND strlen($val)>0 AND (string)$val === (string)$selectedval) ? "selected='selected'" : "") . ">" . $label . "</option>";
            }
            return $ret;
        }
*/
    
?>
