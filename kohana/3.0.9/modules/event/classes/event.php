<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The main event class.
 *
 * @package		Event
 * @author		Oliver Morgan
 * @copyright	(c) 2009 Oliver Morgan
 * @license		MIT
 */
class Event {
	
	/**
	 * An array of instaces created using the factory method.
	 * 
	 * @var	array
	 */
	protected static $_instances;
	
	/**
	 * Retrieves an event from instances, creating one if needed.
	 *
	 * @param	string	The name of the instance.
	 * @return	Event
	 */
	public static function instance($name)
	{
		if ( ! isset(self::$_instances[$name]))
		{
			self::$_instances[$name] = new self($name);
		}

		return self::$_instances[$name];
	}
    
    public static $activeevent = NULL;
	
	/**
	 * The event's identifier.
	 * 
	 * @var	string
	 */
	public $name;
	
	/**
	 * The event's userdata.
	 * 
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * A list of callbacks to be called by the event on invoke.
	 * 
	 * @var	array
	 */
	protected $_callbacks = array();

	/**
	 * Will stop the execution of the event if set to false using stop().
	 *
	 * @var	bool
	 */
	protected $_active = TRUE;
	
	/**
	 * Initializes a new instance of the event object.
	 * 
	 * @param	string	The name of the event.
	 * @return	void
	 */
	protected function __construct($name)
	{
		$this->name = $name;
	}
	
	/**
	 * Returns the current set of data parameters.
	 * 
	 * @param	mixed	Optionally you can return just one, with the key.
	 * @param	mixed	The value to return if the key wasn't found.
	 * @return	mixed
	 */
	public function data($key = NULL, $default = NULL)
	{
		return $key === NULL ? $this->_data : arr::get($this->_data, $key, $default);
	}
	
	/**
	 * Binds an variable by reference.
	 *
	 * @param	string	The key.
	 * @param	mixed	The data.
	 * @return	Event
	 */
	public function bind($key, & $data)
	{
		$this->_data[$key] =& $data;
		
		return $this;
	}
	
	/**
	 * Sets the data variable to a given value.
	 *
	 * @param	string	The key.
	 * @param	mixed	The value.
	 * @return	Event
	 */
	public function set($key, $data)
	{
		$this->_data[$key] = $data;
		
		return $this;
	}
	
	/**
	 * Returns an array of all the callbacks associated with the event.
	 * 
	 * @return array
	 */
	public function callbacks()
	{
		return $this->_callbacks;
	}
	
	/**
	 * Adds a callback to be invoked by the event.
	 * 
	 * @param	mixed	The callback to be invoked.
	 * @return	Event
	 */
	public function callback($callback, $data=NULL)
	{
		$this->_callbacks[] = array("callback" => $callback, "data" => $data);

		return $this;
	}
	
	/**
	 * Resets the callbacks array to an empty array.
	 * 
	 * @return Event
	 */
	public function reset()
	{
		$this->_callbacks = array();
		
		return $this;
	}
	
	/**
	 * Executes the event calling all callbacks with the given data.
	 * 
	 * @return	void
	 */
	public function execute()
	{
        $previousevent = self::$activeevent;
        self::$activeevent = & $this;
		$this->_active = TRUE;

		foreach ($this->_callbacks as $callback)
		{
			if ( $this->_active === FALSE)
			{
				return;
			}
            // If the callback has its own data, simply run that data
            // otherwise, use the Event data and add that
            if (!empty($callback["data"]))
            {
                if (!is_array($callback["data"]))
                {
                    $callback["data"] = array($callback["data"]);
                }
                call_user_func_array($callback["callback"], $callback["data"]);
            }
            else
            {
                 call_user_func_array($callback["callback"], $this->_data);
            }
		}
        
        self::$activeevent = $previousevent;
	}

	/**
	 * Will stop the execution of the event.
	 *
	 * @return void
	 */
	public function stop()
	{
		$this->_active = FALSE;
	}
	
	/**
	 * Returns a data value.
	 * 
	 * @param	mixed	The key index.
	 * @return	mixed
	 */
	public function __get($key)
	{
		return $this->data($key);
	}
	
} // End Event