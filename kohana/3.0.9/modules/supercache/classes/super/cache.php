<?php defined('SYSPATH') or die('No direct script access.');

class Super_Cache {
	
	protected $_run_first = TRUE;
	protected $_classes = array();
	protected $_cached = FALSE;
	
	protected $_directory = '';
	protected $_filename = '';
	
	protected $_lifetime;
	
	public static $loaded_classes = array();   
	
	protected static $_instance = NULL;
	
	public static function instance($uri = NULL, $lifetime = 3600)
	{
		if (self::$_instance)
		{
			self::$_instance->save_cache();
		}
		
		self::$_instance = new Super_Cache($uri, $lifetime);
		
		return self::$_instance;
	}
	
	public function __construct($uri = NULL, $lifetime = 3600)
	{
		if (Kohana::$caching)
		{
			$this->lifetime = $lifetime;
		
			// get string, describing request
			if (empty($uri))
			{
				$request = Request::instance();
				$uri = $request->directory.'.'.$request->controller.'.'.$request->action;
			}
			
			$this->_filename = $uri.'.php';
			// Cache directories are split by keys to prevent filesystem overload
			$this->_directory = Kohana::$cache_dir.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR;
		
			// replace kohana auto load function
			spl_autoload_unregister(array('Kohana', 'auto_load'));
			spl_autoload_register(array($this, 'auto_load'));
			
			// when all done. write files
			register_shutdown_function(array($this, 'shutdown_handler'));
		}
	}
	
	public function auto_load($class)
	{
		if ($this->_run_first)
		{
			$this->_run_first = FALSE;
			
			if (is_file($this->_directory.$this->_filename))
			{
				// cache expired?
				if ((time() - filemtime($this->_directory.$this->_filename)) < $this->lifetime)
				{
					$this->_cached = TRUE;
					require $this->_directory.$this->_filename;
					
					$this->_classes[$class] = $class;
					self::$loaded_classes[$class] = $class;
					
					if (class_exists($class))
						return TRUE;
				} else {
					// Cache has expired
					unlink($this->_directory.$this->_filename);
				}
			}
		}
		
		$result = Kohana::auto_load($class);
		
		$this->_classes[$class] = $class;
		self::$loaded_classes[$class] = $class;
		
		return $result;
	}
	
	public function save_cache()
	{
		spl_autoload_unregister(array($this, 'auto_load'));
		spl_autoload_register(array('Kohana', 'auto_load'));
		
		$this->shutdown_handler();
	}
	
	public function shutdown_handler()
	{
		if ($this->_cached OR empty($this->_classes))
			return;
		
		if ( ! is_dir($this->_directory))
			mkdir($this->_directory, 0777, TRUE);
		
		$this->_cached = TRUE;
		
		$result = fopen ($this->_directory.$this->_filename, "w");
		
		$files = array();
		foreach ($this->_classes as $class)
		{
			$file = str_replace('_', '/', strtolower($class));
			
			if ($path = Kohana::find_file('classes', $file))
			{
				$src = fopen ($path, "r");
				fwrite($result, '<?php if ( ! isset(Super_Cache::$loaded_classes[\''.$class.'\'])):?>');
				stream_copy_to_stream ($src, $result);
				fwrite($result, '?><?php endif?>');
				fclose($src);
			}
		}
		
		fclose ($result);
	}
}
