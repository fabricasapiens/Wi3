<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Routing extends Wi3_Base {
        
        public $protocol; // either HTTP:// or HTTPS://
        
        public $host;  // The host of the site, excluding the protocol (such as http)
        public $domain; // Alias of above
        
        public $completeurl;     // Complete URL, including host, path and query string
        public $url;     // Almost complete URL, with host and path but without query string
        public $querystring;    // Query string. $url + $querystring would give $complete_url
        
        public $controller;  // Current controller
        public $action;  // Current action that is executed
        public $argstring; // The URL-part containing the arguments
        public $args = array(); // The arguments exploded as an array
        
        public $filename; // The filename of the currently executing script, relative to the document root.
        
        public function __construct() 
        {
            $querystringpos = strpos(urldecode($_SERVER["REQUEST_URI"]), "?");
            
            $this->protocol = (stripos($_SERVER["SERVER_PROTOCOL"], "https") === FALSE ? "http://" : "https://");
            $this->host = $this->domain = $_SERVER["HTTP_HOST"];
            $this->url = $this->protocol . $_SERVER["HTTP_HOST"] . ($querystringpos > 0 ? substr(urldecode($_SERVER["REQUEST_URI"]), 0, $querystringpos) : urldecode($_SERVER["REQUEST_URI"]));
            $this->completeurl = $this->protocol . $_SERVER["HTTP_HOST"] . urldecode($_SERVER["REQUEST_URI"]);
            $this->querystring = substr($_SERVER["REQUEST_URI"], $querystringpos);
            
            $this->controller = Request::instance()->controller;
            $this->action = Request::instance()->action;
            
            // If there was a redirect from the Vhost to the Root (because the Vhost folder was entered directly, which is illegal) 
            // Then we want the complete URI to be the arguments of this request
            // And the complete filename simply the ..../index.php/sitearea/view/ + arguments (that is, the URI)
            if (isset($_SERVER["REDIRECT_REDIRECT_REDIRECT_REDIRECTFROMVHOSTTOROOT"])) 
            {
                $this->argstring = trim($_SERVER["REQUEST_URI"],"/");
                $this->filename = $_SERVER["PHP_SELF"].$this->argstring;
            }
            else 
            {
                $this->argstring = Request::instance()->param("args");
                $this->filename = $_SERVER["PHP_SELF"];
            }
            $this->filename = substr($this->filename, 0, -1); // Remove the added slash from the filename!
            $this->args = explode("/",$this->argstring);
        }
        
    }

?>
