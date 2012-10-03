<?php

    // Dummy class used as reference for sitefields. See below.
    Class siteFieldObject {
        public $id = "0";
        public function pk() { 
            return "id"; 
        }
    }

    Class Pagefiller_default extends Wi3_Base 
    {
    
        public static function event($eventtype, $data)
        {
            if ($eventtype == "site_created")
            {
                // Create the table we need for saving the fields of a page
                Wi3::inst()->database->create_table_from_sprig_model("site_field");
            }
        }
    
        public static function view($viewname)
        {
            // Make this pagefiller view extend the base template, with their locations set to the pagefiller folders
            $pagefillerurl = Wi3::inst()->urlof->pagefillerfiles("default");
            $pagefillerpath = Wi3::inst()->pathof->pagefiller("default");
            $pagefillerbaseview = Wi3_Baseview::instance('pagefillerbaseview', array(
                'javascript_url' => $pagefillerurl.'static/javascript/', 
                'javascript_path' => $pagefillerpath.'static/javascript/',
                'css_url' => $pagefillerurl.'static/css/',
                'css_path' => $pagefillerpath.'static/css/',
                'view_path' => $pagefillerpath.'views/',
            )); 
            $pagefillerview = View::factory()->set("this", $pagefillerbaseview);
            $pagefillerview->set_filepath($pagefillerpath.'views/'.$viewname.EXT); // set_filepath sets a complete filename on the View
            //echo Kohana::debug($pagefillerview); exit;
            return $pagefillerview;
        }
        
        // HTML that will be injected in the adminarea-topbar, just beneath the 'content' button
        public function getTopbarHTML($page) {
            // Inject Javascript
            $this->javascript("edittoolbar/ontopbar.js");
            // Load and return HTML
            $iconurl = Wi3::inst()->urlof->pagefillerfiles . "static/images/edittoolbar/";
            $toolbarhtml = $this->view("edittoolbar")->set("iconurl", $iconurl)->set("page", $page)->render();
            $popuphtml = $this->view("popup")->render();
            return $toolbarhtml . $popuphtml;
        }
        
        // Sitearea/Adminarea Render function
        public function render($page)
        {
        
            // Debug: Make sure the 'site_data' and 'site_field' table exists
               //Wi3::inst()->database->create_table_from_sprig_model("site_data");
               //Wi3::inst()->database->create_table_from_sprig_model("site_field");
               
            //-------------------
            // Enable Components
            //-------------------   
               
            // Get component path
            $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/";
            $components = Array();
            // Loop over component-modules and add them
            $dir = new DirectoryIterator( $componentpath );
            foreach($dir as $file) 
            {
                if ($file->isDir() && !$file->isDot()) 
                {
                    $components[] = $file->getPathname();
                }
            }
            Kohana::modules(Kohana::modules() + $components);
        
            //-------------------
            // Raw template 
            //-------------------
            
            // Get template
            $templatename = $page->templatename;
            $templates = Wi3::inst()->configof->site->templates->templates; // Must exist!
            if (isset($templates)) {
                // If there is a templatename set, use that one (if it is available), otherwise use the first that is encountered
                if ($templatename != NULL AND isset($templates->$templatename))
                {
                     $templateconfig = new Wi3_Config(array("configfile" => $templates->$templatename->path."config/config.php"));
                }
            }
            // A page template always extends the base template, with their locations set to the template folders
            $templatebaseview = Wi3_Baseview::instance('templatebaseview', array(
                'javascript_url' => $templates->$templatename->url.'static/javascript/', 
                'javascript_path' => $templates->$templatename->path.'static/javascript/',
                'css_url' => $templates->$templatename->url.'static/css/',
                'css_path' => $templates->$templatename->path.'static/css/',
                'image_url' => $templates->$templatename->url.'static/images/',                
                'view_path' => $templates->$templatename->path.'views/',                
            )); 
            $templateview = View::factory()->set("this", $templatebaseview);
            $templateview->set_filepath($templateconfig->templateview); // set_filepath sets a complete filename on the View
            $html = $templateview->render();
            
            //-------------------
            // Helper functions for editing and viewing
            //-------------------
            function getAllFields($content) {
                $fields = $content->find("cms[type=field]");
                $sitefields = $content->find("cms[type=sitefield]");
                $allfields = Array();
                foreach($sitefields as $pqfield) {
                    $allfields[] = $pqfield;
                }
                foreach($fields as $pqfield) {
                    $allfields[] = $pqfield;
                }
                return $allfields;
            }

            function getField($pqfield, $page) {
                $fieldid = pq($pqfield)->attr("fieldid");
                $fieldname = pq($pqfield)->attr("fieldname");
                $type = pq($pqfield)->attr("type");
                $field = Wi3::inst()->model->factory("site_field");
                if ($type == "field") {
                    if ($fieldid) {
                        return $field->setref($page)->set("id", $fieldid)->load();
                    } else if ($fieldname) {
                        return $field->setref($page)->set("name", $fieldname)->load();
                    }
                } else if ($type == "sitefield") {
                    $siteFieldObject = new siteFieldObject();
                    if ($fieldid) {
                        return $field->setref($siteFieldObject)->set("id", $fieldid)->load();
                    } else if ($fieldname) {
                        return $field->setref($siteFieldObject)->set("name", $fieldname)->load();
                    }
                }
                return $field; // loaded() is false
            }

            //-------------------
            // Editing
            //-------------------
            
            // Check whether the user is editing, and if so, inject the popupdiv and page-id at <body>
            if (Wi3::inst()->routing->controller == "adminarea")
            {
                // TODO: check if user has rights to do this
                Wi3::inst()->plugins->load("plugin_jquery_wi3");
                $this->javascript("edittoolbar/onpage.js");
                $this->javascript("jq-wysihat.js");
                $this->javascript("rangy-core.js");

                // Insert Popup
                $popuphtml = $this->view("popup")->render();
                $html = preg_replace("@<body[^>]*>@","$0".$popuphtml,$html);

                // Insert Page-ID
                $pageidhtml = $this->view("pageid")->set("page", $page)->render();
                $html = preg_replace("@<body[^>]*>@","$0".$pageidhtml,$html);
                
                // Replace all the <cms> blocks with the appropriate content
                $html = phpQuery::newDocument($html); // Give PHPQuery a context to work with

                function replacePQFieldsWithAdminHTML($content,$page,$controller) {
                    $allfields = getAllFields($content);
                    foreach($allfields as $pqfield)
                    {
                        $field = getField($pqfield, $page);
                        if (!$field->loaded())
                        {
                            // Create field
                            $fieldtype = pq($pqfield)->attr("fieldtype");
                            if (pq($pqfield)->attr("type") == "field") {
                                $ref = $page;
                            } else {
                                $ref = new siteFieldObject();
                            }
                            $field = Wi3::inst()->model->factory("site_field")->setref($ref)->set("type", $fieldtype);
                            // Store name, if present
                            $fieldname = pq($pqfield)->attr("fieldname");
                            if ($fieldname) {
                                $field->set("name", $fieldname);
                            }
                            $field->create();
                        }
                        if ($field->loaded())
                        {
                            $postprocessingid = Wi3::inst()->date_now(); // a unique id for a field is necessary to be able to match properly in the case of nested fields
                            $wraphtml = "<postprocessing" . $postprocessingid . ">" . $controller->view("fieldrender_edit")->set("field", $field)->set("pqfield", $pqfield)->render() . "</postprocessing" . $postprocessingid . ">"; // the <postprocessing> tags are, obviously, for postprocessing. See below
                            pq($pqfield)->replaceWith($wraphtml);
                        }
                    }
                }

                //-------------------
                // Fields outside editable blocks
                //-------------------
                replacePQFieldsWithAdminHTML($html,$page,$this);

                //-------------------
                // Editable blocks and the fields therein
                //-------------------
                $editableblocks = $html->find("cms[type=editableblock]");
                foreach($editableblocks as $editableblock)
                {
                    $name = pq($editableblock)->attr("name");
                    // Try to load up to date content for this block, otherwise show the default content 
                    $content = $page->loadEditableBlockContent($editableblock, $name);
                    // Replace the <cms type='field'> blocks and expand them into real field-renders
                    $content = phpQuery::newDocument($content);
                    replacePQFieldsWithAdminHTML($content,$page,$this);
                    $blockcontent = "<div type='editableblock' name='" . $name . "' contenteditable='true'>" . $content . "</div>";
                    pq($editableblock)->replaceWith($blockcontent);
                }
            }
            
            //-------------------
            // Pure viewing
            //-------------------
            
            else
            {
                // Simply display the contents of the block, not making them editable
                $html = phpQuery::newDocument($html); // Give PHPQuery a context to work with

                function replacePQFieldsWithViewHTML($content,$page) {
                    $allfields = getAllFields($content);
                    foreach($allfields as $pqfield)
                    {
                        $field = getField($pqfield, $page);
                        if (!$field->loaded())
                        {
                          
                            // Create field
                            $fieldtype = pq($pqfield)->attr("fieldtype");
                            if (pq($pqfield)->attr("type") == "field") {
                                $ref = $page;
                            } else {
                                $ref = new siteFieldObject();
                            }
                            $field = Wi3::inst()->model->factory("site_field")->setref($ref)->set("type", $fieldtype);
                            // Store name, if present
                            $fieldname = pq($pqfield)->attr("fieldname");
                            if ($fieldname) {
                                $field->set("name", $fieldname);
                            }
                            $field->create();
                        }
                        if ($field->loaded()){
                            // Get set style
                            $style = pq($pqfield)->attr("style");
                            $field->options["style"] = $style;
                            // Get style options
                            $stylearray = Array();
                            $stylearray["float"] = pq($pqfield)->attr("style_float");
                            $stylearray["padding"] = pq($pqfield)->attr("style_padding");
                            $stylearray["width"] = pq($pqfield)->attr("style_width");
                            // Only set an explicit display block if no display is found in '$style'
                            if (strpos($style, "display:") === false) {
                                 $stylearray["display"] = "block";
                            }
                            $field->options["stylearray"] = $stylearray;
                            // Render the field, in which the field can also change the style options
                            $fieldhtml = $field->render();
                            // The field can override these options, if it wants
                            $style = $field->options["style"];
                            $stylearray = $field->options["stylearray"];
                            // Once the field is rendered, it is known whether it wants to be an inline element, or a block element
                            // Use float and padding only if element is not inline
                            if (strpos($style, "display:inline") !== false || 
                                (isset($stylearray["display"]) && $stylearray["display"] == "inline"))
                            {
                                unset($stylearray["float"]);
                                unset($stylearray["padding"]);
                            }
                            // Calculate total style
                            $totalstyle = $style;
                            foreach($stylearray as $name => $val) {
                                if (!empty($val)) {
                                    $totalstyle .= "; " . $name . ":" . $val;
                                }
                            }
                            $totalstyle .= "; position: relative;";
                            // Replace the <cms> part with a render of the field
                            $postprocessingid = Wi3::inst()->date_now(); // a unique id for a field is necessary to be able to match properly in the case of nested fields
                            $wraphtml = "<postprocessing" . $postprocessingid . "><div type='field' fieldid='" . $field->id . "' style='" . $totalstyle . "' contenteditable='false'>" . $fieldhtml . "</div></postprocessing" . $postprocessingid . ">";
                            pq($pqfield)->replaceWith($wraphtml);
                        }
                    }
                }

                //-------------------
                // Fields outside editable blocks
                //-------------------
                replacePQFieldsWithViewHTML($html,$page);

                //-------------------
                // Editable blocks and the fields therein
                //-------------------
                $editableblocks = $html->find("cms[type=editableblock]");
                foreach($editableblocks as $editableblock)
                {
                    $name = pq($editableblock)->attr("name");
                    $id = pq($editableblock)->attr("id");
                    // Try to load up to date content for this block, otherwise show the default content 
                    $content = $page->loadEditableBlockContent($editableblock, $name);
                    // Replace the <cms type='field'> and <cms type='sitefield'> blocks and expand them into real field-renders
                    // For normal fields, the fieldid is unique for the page
                    // For sitefields, the fieldid is unique for the site, using the siteFieldObject
                    // Example layout:
                    /**
                    * <cms type='field' fieldtype='image' fieldname='uniqueid' style_float="left" style_padding="20px">
                    * </cms>
                    */                   

                    $content = phpQuery::newDocument($content);
                    replacePQFieldsWithViewHTML($content,$page);
                    
                    // Create block, and add id if it was present in the <cms> block
                    if (!empty($id))
                    {
                        $blockcontent = "<div style='display:inline-block; width: 100%;' id='" . $id . "' type='contentblock' name='" . $name . "'>" . $content . "</div>";
                    }
                    else
                    {
                        $blockcontent = "<div style='display:inline-block; width: 100%;' type='contentblock' name='" . $name . "'>" . $content . "</div>";
                    }
                    pq($editableblock)->replaceWith($blockcontent);
                }
            }
            
            // Postprocessing on the html (also executed while saving the blocks in controller/pagefiller/default/edittoolbar/ajax.php)
            // There is a 'bug' within phpQuery that it wants no DIVs within P elements, even not when they are display: inline-block;
            // To circumvent this, we have added <postprocessing[number]> tags, so that PHPQuery does not close the <p> before the </div>
            // Now, we only have to remove the <postprocessing> tags again
            $html = preg_replace("@<(postprocessing[0-9]*)>(.*)</\\1>@si", "$2", $html); // The \\1 is an in-pattern backreference. The flag i results in insensitivity for case. The s flag makes the string a single line without \r and \n
            
            return $html;
        }
        
        // Adminarea add-page functions
        public function javascript($filename)
        {
            Wi3::inst()->javascript->add(Wi3::inst()->urlof->pagefillerfiles("default") . "static/javascript/" . $filename, "view");
        }
        
        //-----------
        // Mandatory new-page and page-editings functions
        //-----------
        
        // Function to return the html for the page-adding options
        public function pageoptionshtml()
        {
            
            // Disabled, because it often does not work for hidden elements, and even if so, the shadow does not appear or on the wrong place :s
            //Wi3::inst()->plugins->load("plugin_jquery_dropshadow"); // Make dropshadows available
            //$ret = "<script>\$('#wi3_add_pages > a').next().show(); \$(function(){\$('.pagefiller_default_dropzonepreview').dropshadow();}); </script>";
            
            // Include javascript file that handles some addpageoptions-refresh stuff
            $this->javascript("pageoptionshtml.js");
            
            return $this->pageoptionshtmlfortemplate(NULL, NULL);
        }
        
        public function pageoptionshtmlfortemplate($templatename = NULL, $dropzonepresetname = NULL)
        {
            $ret = "<div style='margin-top: 10px; height: 200px; position: relative;'>";
            
            $templates = Wi3::inst()->configof->site->templates->templates; // Must exist!
            if (isset($templates)) {
                // If there is a templatename set, use that one (if it is available), otherwise use the first that is encountered
                if ($templatename != NULL AND isset($templates->$templatename))
                {
                     $templateconfig = new Wi3_Config(array("configfile" => $templates->$templatename->path."config/config.php"));
                }
                else
                {
                    // Get the first template, as a basis for the other options
                    foreach($templates as $templatename => $template) {
                        $templateconfig = new Wi3_Config(array("configfile" => $template->path."config/config.php"));
                        break;
                    }
                }
                // Now render the choices for the different templates, dropzonepresets and elementstyles/themes
                $ret .= "<div style='position:absolute; left: 310px; top: 0px; width: 170px; height: 200px;'>";
                    // Templates
                    $ret .= "<label style='padding-top: 0px;'>Template</label><select name='templatename'  id='pagefiller_default_templatename' onChange='wi3.pagefillers.default.reloadpageoptionshtml();' >";
                    foreach($templates as $configtemplatename => $template) 
                    {
                        $ret .=  "<option ";
                        if ($configtemplatename == $templatename) { $ret .= " selected='selected' "; }
                        $ret .=  " value='".$configtemplatename."'>".$template->title."</option>";
                    }
                    $ret .=  "</select>";
                    /*
                    // List of available dropzone presets
                    $dropzonepresets = Wi3::inst()->configof->site->dropzonepresets->dropzonepresets; // Must exist!
                    $ret .= "<label>Vulling</label><select name='pagefiller_dropzonepreset' id='pagefiller_default_dropzonepreset' onChange='wi3.pagefillers.default.reloadpageoptionshtml();'>";
                    $selectedpreset = "";
                    foreach($dropzonepresets as $presetname => $preset) 
                    {
                        // Now load the actual presetconfig, as pointed to in the site-list of dropzonepresets
                        $presetconfig = new Wi3_Config(array("configfile" => $preset->path));
                        // Load the dropzones that are required by the template
                        $requireddropzones = $templateconfig->dropzones;
                        $presetdropzones = $presetconfig->dropzonepreset->dropzones;
                        // Now check whether this preset can fill all the required dropzones
                        $presetcoversalldropzones = TRUE;
                        foreach($requireddropzones as $dropzonename)
                        {
                            if (isset($presetdropzones->{$dropzonename}) == FALSE)
                            {
                                $presetcoversalldropzones = FALSE;
                                break;
                            }
                        }
                        if ($presetcoversalldropzones == FALSE)
                        {
                            continue; // Preset does not have the required dropzones
                        }
                        // Set the selectedpreset, for use below
                        // This can be either through the $dropzonepresetname, or if that is not given, simply the first encountered preset in the list
                        $selected = "";
                        if ($dropzonepresetname != NULL)
                        {
                            if ($presetname == $dropzonepresetname)
                            {
                                $selectedpreset = $presetconfig;
                                $selected  = " selected='selected' ";
                            }
                        }
                        else if (empty($selectedpreset))
                        {
                            $selectedpreset = $presetconfig;
                            $selected  = " checked='checked' ";
                        }
                        $ret .=  "<option " . $selected  . " value='".$presetname."'>".$preset->title."</option>";
                    }
                    $ret .=  "</select>";
                    */
                $ret .=  "</div>";
                
                // Display the template image, with the template-dropzones. Those latter are then filled with the dropzonesettings of the first preset
                $ret .= "<div style='position: absolute; left: 0px; top: 0px;'>";
                    $ret .= "<img style='width: 300px; height: 200px;' src='". $templateconfig->preview->imageurl . "'/>";
                    $previewdropzones = $templateconfig->preview->dropzones;
                    foreach($previewdropzones as $dropzonename => $dropzone)
                    {
                        $position = $dropzone->position;
                        // These classes are  transparent, so a text-color can be chosen for maximum contrast
                        if (isset($dropzone->textcolor))
                        {
                            $textcolor = $dropzone->textcolor;
                        }
                        else
                        {
                            $textcolor = "#000"; // black for default
                        }
                        $ret .= "<div class='pagefiller_default_dropzonepreview' id='pagefiller_default_dropzonepreview_dropzone_'" . $dropzonename . "' style='color: ".$textcolor."; position: absolute; left: ".$position->left."px; top:".$position->top."px; width:".$position->width."px; height:".$position->height."px;'>";
                        /*
                        // Now insert the text of the first preset
                        $dropzonefields = $selectedpreset->dropzonepreset->dropzones->$dropzonename->defaultfields;
                        foreach($dropzonefields as $fieldname => $fieldconfig)
                        {
                            // If there is no config, but a simple string that identifies the fieldname
                            if (is_string($fieldconfig))
                            {
                                $fieldtitle = $fieldconfig;
                            }
                            else
                            {
                                $fieldtitle = $fieldconfig->title;
                            }
                            $ret .= $fieldtitle . "<br />";
                        }
                        */
                        $ret .= "</div>";
                    }
                $ret .= "</div>";
                
            }
            
            $ret .= "</div>";
            return $ret;
        }
        
        public function pageoptionstemplatehtml()
        {
            return $this->pageoptionstemplatehtmlfortemplate(NULL);
        }
        
        public function pageoptionstemplatehtmlfortemplate($templatename = NULL)
        {
            $ret = "<div style='margin-top: 10px; position: relative;' id='pageoptionstemplate'>";
            
            $templates = Wi3::inst()->configof->site->templates->templates; // Must exist!
            if (isset($templates)) {
                // If there is a templatename set, use that one (if it is available), otherwise use the first that is encountered
                if ($templatename != NULL AND isset($templates->$templatename))
                {
                     $templateconfig = new Wi3_Config(array("configfile" => $templates->$templatename->path."config/config.php"));
                }
                else
                {
                    // Get the first template, as a basis for the other options
                    foreach($templates as $templatename => $template) {
                        $templateconfig = new Wi3_Config(array("configfile" => $template->path."config/config.php"));
                        break;
                    }
                }
                // Now render the choices for the different templates, dropzonepresets and elementstyles/themes
                $ret .= "<div>";
                    // Templates
                    //$ret .= "<label style='padding-top: 0px;'>Template</label>";
                    $ret .= "<select style='width: 300px;' name='pagefiller_templatename'  id='pagefiller_default_editpage_templatename' onChange='wi3.pagefillers.default.reloadpageoptionstemplatehtml()();' >";
                    foreach($templates as $configtemplatename => $template) 
                    {
                        $ret .=  "<option ";
                        if ($configtemplatename == $templatename) { $ret .= " selected='selected' "; }
                        $ret .=  " value='".$configtemplatename."'>".$template->title."</option>";
                    }
                    $ret .=  "</select>";
                $ret .= "</div>";
                
                // Display the template image, with the template-dropzones. Those latter are then filled with the dropzonesettings of the first preset
                $ret .= "<div>";
                    $ret .= "<img style='width: 300px; height: 200px;' src='". $templateconfig->preview->imageurl . "'/>";
                $ret .= "</div>";
                
            }
            
            $ret .= "</div>";
            return $ret;
        }
        
    }
    
?>
