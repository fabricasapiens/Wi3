<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Auth_Global extends Wi3_Base
    {
        
        public $_auth;
        
        public $user;
        
        public $_user_model = "User";
        public $_token_model = "User_Token";
        
        // Always try to login user
        public function __construct() 
        {
            // Note: it is not recommended to call Auth_Sprig::instance() for both global and siteauth, since that will return a singleton everytime, and this will get conflicts between globalauth and siteauth, if they are used at the same time
            // Manually loading the config and creating a new Auth_Sprig like
            // $conf = Kohana::config("auth");
            // $inst = new Auth_Sprig($conf); 
            // Does also not work, since this will (assumably) call the new Auth() twice,
            // where somewhere the salt_pattern gets rewritten to an array, causing an error the next time the Auth() is generated
            $this->_auth = Auth_Sprig::instance();
            $this->_auth->user_model($this->_user_model);
            $this->_auth->token_model($this->_token_model);
            
            $this->session = Session::instance();
            
            if ($this->logged_in() OR $this->auto_login() == TRUE)  
            {
                // Logged in
                $this->user = $this->get_user();
                // Set cache-addendum
                // TODO: Wi3::$cache->page_addendums["wi3_login_userid"] = $this->user->id;
                $this->session->set("userid", $this->user->id);
            }
        }
        
        // Pass functions to the auth instance
        // Effectively, $this->auth is the 'same' as just using $this. 
        // Anyone thus can call Wi3::inst()->globalauth->login(...) and this will route to Wi3::inst()->globalauth->_auth->login(...)
        public function __CALL($name, $args)
        {
            $r = new ReflectionClass($this->_auth);
            return $r->getMethod($name)->invokeArgs($this->_auth, $args);
        }
        
        // Pass GET and SET to the auth instance
        public function __GET($key)
        {
            return $this->_auth->{$key};
        }
        public function __SET($key, $val)
        {
            return $this->_auth->{$key} = &$val;
        }
        
    }

?>