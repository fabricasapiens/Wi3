<?php

	// TODO: move this to tikoCache plugin and listen for events in the init.php
    // Define the TikoCacheAPI. It is the interface between a Cache and the Application
    class Wi3TikoCacheAPI implements TikoCacheAPI {

        private $item = null;
        private $cache;
        private $request;

        public function __construct($cache, $request) {
            $this->cache = $cache;
            $this->request = $request;
        }

        public function getUrl() {
            return $_SERVER["SCRIPT_URI"];
        }

        public function get($key) {
            $result = $this->cache->get($key);
            if (!$result) {
                return null;
            }
            return $result;
        }

        public function set($key, $value) {
            $this->cache->set($key,$value);
        }

        public function resolve($item) {
            // Immediately return
            $this->request->response = $item;
            throw new Exception_Continue(); // Will break out of this try catch
        }

        public function getItem() {
            return $this->item;
        }

        public function remove($key) {
            $this->cache->delete($key);
        }
    }

    // Listen for events
    Event::instance('wi3.beforeinit')->callback(array('Wi3TikoCache','beforeInit'));
	Event::instance('wi3.afterexecution.processcontent')->callback(array('Wi3TikoCache','afterExecution'));

    class Wi3TikoCache extends Wi3_Base {

    	static $tiko;
        static $doCache = true;

    	public static function beforeInit() {
			$cache = Cache::instance('file');
		    $cacheAPI = new Wi3TikoCacheAPI($cache,Request::instance());
		    self::$tiko = new tikoCacheController($cacheAPI);
    	}

    	public static function afterExecution() {
            if (self::$doCache) {
    		  self::$tiko->cache(Request::instance()->response);
            }
    	}

        public function doNotCache() {
            self::$doCache = false;
        }

        public function requireCacheParameter($key) {
            return self::$tiko->requireCacheParameter($key);
        }

        public function fillCacheParameter($key,$value) {
            return self::$tiko->fillCacheParameter($key,$value);
        }

    }

?>