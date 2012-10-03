<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Pagefiller_Default_Component_Simpleblogoverview extends Pagefiller_Default_Component_Base
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
            // Return false, so the field will try to load the field on its own, which will fail so the content between the <cms> tag is loaded
            return false;
        }

        public function saveEditableBlockData($field, $blockName, $content) {
            // We get here for all the cms fields that are found within this field
            // Every cms block belongs to one blogarticle field, so we need to find that field and save the data to it
            $blogarticlefieldid = substr($blockName, 6);
            $blogarticlefield = $this->getBlogField($blogarticlefieldid);
            $this->fielddata($blogarticlefield,"summary",$content);
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
                $page = Wi3::inst()->model->factory("site_page")->values(Array("id"=>$blogfield->_refid))->load();
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
			return $this->view("render")->set("data", $dataobject)->set("articles", $articles)->render();
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
