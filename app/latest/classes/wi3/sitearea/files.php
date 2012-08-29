<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Model interface for Wi3
 * @author	Willem Mulder
 */
// This class provides a manager for adding and deleting files
// Files are loaded via the site_* models, so that the database-name "site" is used. (Wi3 will have assigned the actual site-database-config to this "site"-db. See wi3.php)
class Wi3_Sitearea_Files extends Wi3_Base {

    public $allfiles = NULL;

    // call add($file, $settings) or add($fileproperties, $settings)
    // TODO: implement this structure as well in page and pageposition! ($page, $settings) and ($pageposition, $settings)
    function add($file = array(), $settings = array()) {
        if (is_object($file)) {
            $new = $file; // File is provided through parameter
        } else {
            // No file object, but fileproperties are provided
            $new = Wi3::inst()->model->factory("site_file");
            $fields = $new->fields();
            foreach ($file as $key => $val) {
                // Only add the value if it is present somewhere in the model
                if (isset($fields[$key])) {
                    $new->{$key} = $val;
                }
            }
        }
        // Insert the file at the appropriate place
        // Check that with an existing file 
        $existing = Wi3::inst()->model->factory("site_file");
        if (isset($settings["under"])) {
            $existing->id = $settings["under"];
        } else {
            $existing->lft = 1;
        }
        $existing->load();
        if ($existing->loaded()) {
            if (isset($settings["under"])) {
                $new->insert_as_first_child($existing);
            } else {
                $new->insert_as_prev_sibling($existing);
            }
        } else {
            $new->insert_as_new_root();
        }
        // Return inserted file
        return $new;
    }

    public function moveBefore($file, $reffile) {
        //create Model objects if just IDs are given
        if (is_numeric($file)) {
            $file = Wi3::inst()->model->factory("site_file", array("id" => $file))->load();
        }
        if (is_numeric($reffile)) {
            $reffile = Wi3::inst()->model->factory("site_file", array("id" => $reffile))->load();
        }

        if ($reffile AND $file) {
            $file->move_to_prev_sibling($reffile);
            $file->reload();
            return true;
        }
    }

    public function moveAfter($file, $reffile) {
        //create Model objects if just IDs are given
        if (is_numeric($file)) {
            $file = Wi3::inst()->model->factory("site_file", array("id" => $file))->load();
        }
        if (is_numeric($reffile)) {
            $reffile = Wi3::inst()->model->factory("site_file", array("id" => $reffile))->load();
        }

        if ($reffile AND $file) {
            $file->move_to_next_sibling($reffile);
            $file->reload();
            return true;
        }
    }

    public function moveUnder($file, $reffile) {
        //create Model objects if just IDs are given
        if (is_numeric($file)) {
            $file = Wi3::inst()->model->factory("site_file", array("id" => $file))->load();
        }
        if (is_numeric($reffile)) {
            $reffile = Wi3::inst()->model->factory("site_file", array("id" => $reffile))->load();
        }

        if ($reffile AND $file AND $reffile->type == "folder") {
            $file->move_to_last_child($reffile);
            $file->reload();
            return true;
        } else {
            return false;
        }
    }

    public function delete($file) {
        //create Model objects if just IDs are given
        //new Profiler();
        if (is_numeric($file)) {
            $file = Wi3::inst()->model->factory("site_file", array("id" => $file))->load();
        }
        // Folder are only present in the DB, not on disk
        if ($file->type == "file") {
            // TODO: delete all possible resized versions of images!
            if (file_exists(Wi3::inst()->pathof->site . "data/uploads/" . $file->filename)) {
                unlink(Wi3::inst()->pathof->site . "data/uploads/" . $file->filename);
            }
        }
        // Deleting a node in a tree will delete its descendants as well
        // The model will take care of that
        $file->delete();
        //echo View::factory("profiler/stats");
        return true;
    }

    public function getall() {
        if ($this->allfiles == NULL) {
            $this->allfiles = Wi3::inst()->model->factory("site_file")->load(NULL, FALSE); // False for no limit on results
        }
        return $this->allfiles;
    }

    // Function to find certain files, based on specific requirements
    // @param Array $filteroptions [ "extensions" ]
    // TODO: let this function return a tree
    public function find($filteroptions) {

        $file = Wi3::inst()->model->factory("site_file");

        // Create query to find all the files, based on the filteroptions
        $query = DB::select();
        if (isset($filteroptions["extensions"])) {
            if (count($filteroptions["extensions"]) > 0) {
                $counter = 0;
                foreach ($filteroptions["extensions"] as $ext) {
                    $counter++;
                    if ($counter == 1) {
                        $query = $query->where("filename", "LIKE", "%." . $ext);
                    } else {
                        $query = $query->or_where("filename", "LIKE", "%." . $ext);
                    }
                }
            }
        }

        $query = $query->order_by($file->left_column, "ASC");

        $files = $file->load($query, NULL);

        return $files;
    }

    // Function to find certain folders, based on specific requirements
    // @param Array $filteroptions [ "fileextensions" ]
    public function findfolders($filteroptions) {
        // Do a very inefficient fetch of all folders
        // TODO: make this more efficient
        $file = Wi3::inst()->model->factory("site_file");
		// old: ->where("ABS(\"".$file->right_column."\"-\"".$file->left_column."\")", ">", 1)
        $query = DB::select()->where("type", "=", "folder")->order_by($file->left_column);
        $folders = $file->load($query, NULL);
        // Limit the folders to those that contain files that satisfy the filteroptions
        if (isset($filteroptions["fileextensions"])) {
            $files = $this->find(Array("extensions" => $filteroptions["fileextensions"]));
            // Return the folders that contain the found files
            $returnfolders = Array();
            foreach($folders as $folderid => $folder) {
                // Check if this folder contains any found file
                foreach ($files as $fileid => $file) {
                    if ($folder->{$folder->left_column} < $file->{$file->left_column} AND $folder->{$folder->right_column} > $file->{$file->right_column} ) {
                        $returnfolders[$folderid] = $folder;
                        break;
                    }
                }
            }
            return $returnfolders;
        } else {
            // If no file-conditions, then just return all the folders that contain files
            return $folders;
        }
    }

}

?>
