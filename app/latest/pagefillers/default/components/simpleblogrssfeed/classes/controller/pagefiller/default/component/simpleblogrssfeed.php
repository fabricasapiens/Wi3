<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Simpleblogrssfeed extends Controller_Pagefiller_Default_Component_Base
    {
    
		public static $componentname = "simpleblogrssfeed";
		
        public function before() 
        {
            Wi3::inst()->acl->grant("*", $this, "rssfeed"); // Everybody can access rssfeed function in this controller
            parent::before();
        }

        public function startEdit($field) 
        {
            // Possibly custom code here
        }
        
        public function edit($field) 
        {
    		// custom code
            $this->fielddata($field, "edittimestamp", time());
        }

        public function action_rssfeed() {
            // Feed always includes 20 latest items
            $amount = 20;
            // TODO: load information from field-instance using an id
            $dataobject = Array();
            $articles = Array();
            // TODO: Rework this or at least implement caching. Right now it is completely inefficient
            // 1. Fetch the latest [$amount] fields with type simpleblogarticle
            $fields = Wi3::inst()->model->factory("site_field")->values(Array("type"=>"simpleblogarticle"))->load(
                DB::select()->order_by("id"), 
                $amount
            );
            // 2. Grab their data
            foreach($fields as $blogfield) {
                // Get page where this field is situated on and include its URL
                $page = Wi3::inst()->model->factory("site_page")->values(Array("id"=>$blogfield->_refid))->load();
                $pageurl = Wi3::inst()->urlof->page($page);
                // Load data
                $data = $this->fielddata($blogfield);
                $data->pageurl = $pageurl;
                $image = Wi3::inst()->model->factory("site_file")->values(Array("id"=>$data->image))->load();
                $imageurl = Wi3::inst()->urlof->image($image,300);
                $data->imageurl = $imageurl;
                $articles[] = $data;
            }
            echo $this->view("rssfeed")->set("data", $dataobject)->set("articles", $articles)->render();
            // Content-Type? Request::current()->headers["Content-Type"] = "xml/text";
            // File? Request::current()->send_file(true,"rssfeed.xml");
        }
        
    }

?>
