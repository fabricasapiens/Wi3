<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Simpleblogsearch extends Pagefiller_Default_Component_Base
    {

        // Model
        public static $model = Array(
            "amount" => Array("type" => "number"),
            "entertimestamp" => Array("type" => "text", "showoneditscreen" => false),
            "edittimestamp" => Array("type" => "text", "showoneditscreen" => false)
        );
    

        // This function receives all sorts of events related to the field with this type
        public function fieldevent($eventtype, $field)
        {
            if ($eventtype == "create")
            {
                // Set the inserttype
				// TODO: think about this. It doesn't feel right.
                Controller_Pagefiller_Default_Edittoolbar_Ajax::$responseoptions["inserttype"] = "replace";
                // Create the data that is associated with this field
                $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->create();
                $this->fielddata($field, "entertimestamp", time());
            }
            else if ($eventtype == "delete")
            {
                Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->delete();
            }
        }

        public function loadEditableBlockData($field, $blockName) {
            // Return false, so the pagefiller will try to load the field on its own, which will fail so the content between the <cms> tag is loaded
            return false;
        }

        public function saveEditableBlockData($field, $blockName, $content) {
            // Don't save any data
            return false;
        }
    
        public function render($field)
        {
			$dataobject = $this->fielddata($field);
            if (!isset($dataobject->amount)) {
                $this->fielddata($field, "amount", 10);
            }
            $articles = Array();
            // TODO: Rework this or at least implement caching. Right now it is completely inefficient
            // 1. Fetch the latest [$amount] fields with type simpleblogarticle
            $fields = $this->getAllBlogFields($dataobject->amount);
            // 2. Grab their data
            foreach($fields as $blogfield) {
                // Get page where this field is situated on and include its URL
                $page = $blogfield->getParentPage();
                $pageurl = Wi3::inst()->urlof->page($page);
                // Load data
                $data = $this->fielddata($blogfield);
                $data->fieldid = $blogfield->id;
                $data->pageurl = $pageurl;
                $image = Wi3::inst()->model->factory("site_file")->values(Array("id"=>$data->image))->load();
                $imageurl = Wi3::inst()->urlof->image($image,300);
                $data->imageurl = $imageurl;
                $articles[] = $data;
            }
            $pages = Wi3::inst()->sitearea->navigation->menu->loadPages(); // load pages under pagepositions
			return $this->view("render")->set("pages", $pages)->set("data", $dataobject)->set("articles", $articles)->render();
        }

        private function getAllBlogFields($limit=0) {
            return Wi3::inst()->model->factory("site_field")->values(Array("type"=>"simpleblogarticle"))->load(
                DB::select()->order_by("id"), 
                $limit
            );
        }

        private function getBlogField($id) {
            return Wi3::inst()->model->factory("site_field")->values(Array("type"=>"simpleblogarticle", "id" => $id))->load();
        }
    }

?>
