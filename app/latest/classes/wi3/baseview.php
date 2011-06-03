<?php

    // This class can be used as the $this in any view (be it in admin-, superadmin-, or site-area)
    // If it is bound as 'this' to that specific view
    class Wi3_Baseview extends Wi3_Base
    {
        
        public $_view;
        public $_params = array();
        
        function __construct($params = array()) 
        {
            $this->_params = $params;
        }
        
        public function javascript($name) 
        {
            // Javascript will be inserted right before </head>, and if no placeholders are specified, this order will be obeyed
            // 1. Wi3-type javascript
            // 2. Plugin-type javascript
            // 3. Pagefiller-javascript
            // 4. View-type javascript
            // However, the request-response can contain placeholders, so that i.e. View-type javascript is inserted before Plugins etc
            if (is_array($name)) 
            {
                foreach($name as $n) 
                { 
                    Wi3::inst()->javascript->add($this->_params["javascript_url"] . $n, "view");
                }
            }
            else
            {
                Wi3::inst()->javascript->add($this->_params["javascript_url"] . $name, "view");
            }
        }
        
        public function css($name) 
        {
            if (is_array($name)) 
            {
                foreach($name as $n) 
                { 
                    Wi3::inst()->css->add($this->_params["css_url"] . $n, "view");
                }
            }
            else
            {
                Wi3::inst()->css->add($this->_params["css_url"] . $name, "view");
            }
        }
        
        public function view($name)
        {
            return View::factory()->set_filepath($this->_params["view_path"] . $name . EXT);
        }
        
       	/**
         * Captures the output that is generated when a view is included.
         * The view data will be extracted to create local variables.
         * The function is not static, to allow for scope resolution ($this reference)
         *
         *     $output = View::capture($file, $data);
         *
         * @param   string  filename
         * @param   array   variables
         * @return  string
         */
        public function capture($kohana_view_filename, array $kohana_view_data)
        {
            
            // Import the view variables to local namespace
            extract($kohana_view_data, EXTR_SKIP);

            if (View::$_global_data)
            {
                // Import the global view variables to local namespace and maintain references
                extract(View::$_global_data, EXTR_REFS);
            }

            // Capture the view output
            ob_start();

            try
            {
                // Load the view within the current scope
                include $kohana_view_filename;
            }
            catch (Exception $e)
            {
                // Delete the output buffer
                ob_end_clean();

                // Re-throw the exception
                throw $e;
            }

            // Get the captured output and close the buffer
            return ob_get_clean();
        }
    }

?>
