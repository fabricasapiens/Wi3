<?php defined('SYSPATH') or die('No direct script access.');

    class Wi3_Database {
        
        public $_engine = "INNODB"; // Or MYISAM
        
        public static function instance($type = NULL, $configarray = NULL) 
        {
            if ($type=="global" OR $type==NULL)
            {
                return Database::instance("global", $configarray);
            } 
            elseif ($type == "site")
            {
                // Since a site is loaded as a module, we can load the Database config array from the site/config/database.php right away
                return Database::instance("site", $configarray);
            }
        }
        
        // Functions to create databases, create tables, alter tables etc
        //
        public function create_database($name) 
        {
            // Use the global database settings to gain access to MySQL (username/password)
            return Wi3::inst()->globaldatabase->query(NULL, "CREATE DATABASE  `".$name."`",TRUE);
        }
        
        public function delete_database($name) 
        {
            // Use the global database settings to gain access to MySQL (username/password)
            return Wi3::inst()->globaldatabase->query(NULL, "DROP DATABASE  `".$name."`",TRUE);
        }
        
        public function create_table_from_sprig_model($m) 
        {
            // Create Model if only string is passed
            if (is_string($m)) 
            {
                $m = Sprig::factory($m);
            }
            $sqls = $this->sql_for_creating_table_from_sprig_model($m);
            $sqls += $this->sql_for_creating_connection_table_from_sprig_model($m);
            
            $db = Wi3::instance()->database->instance($m->_db); //use correct global or site db-instance, depending on the setting of the model
            $result = Array();
            foreach($sqls as $name => $sql) {
                $res = $db->query(NULL, $sql, TRUE);
                if ($res !== FALSE) 
                {
                    $result[$name] = $sql;
                }
                else
                {
                    $result[$name] = FALSE;
                }
            }
            return $result;
        }
        
        public function sql_for_creating_table_from_sprig_model($m)
        {
            // Create Model if only string is passed
            if (is_string($m)) 
            {
                $m = Sprig::factory($m);
            }
            
            // Loop through the fields and create a table from that
            $sql = "CREATE TABLE IF NOT EXISTS `".$m->table()."` (";
            $sqladdendum = "";
            foreach ($m->fields() as $name => $field)
            {
                if ($field->in_db === TRUE) 
                {
                    // Creating the per-column SQL
                    $sql .= "`".$field->column."` "; // Column-Name
                    if (is_a($field, "Sprig_Field_Integer")) 
                    {
                        $sql .= "INT UNSIGNED "; // Assuming no negative values
                    } 
                    elseif (is_a($field, "Sprig_Field_Boolean")) 
                    {
                        $sql .= "BOOL ";
                    }
                    elseif (is_a($field, "Sprig_Field_Text")) 
                    {
                        $sql .= "TEXT ";
                    }
                    else // In other cases, including if (is_a($field, "Sprig_Field_Char")) 
                    {
                        $sql .= "VARCHAR(255) ";
                    } 
                    $sql .= ($field->empty === TRUE ? "NULL" : "NOT NULL")." "; // Can be NULL?
                    if ($field instanceof Sprig_Field_Auto)
                    {
                        $sql .= "AUTO_INCREMENT ";
                    }
                    $sql .= ",";
                    // Now checking if there is need for additional clauses like PRIMARY KEY or UNIQUE KEY
                    if ($field->primary === TRUE) 
                    {
                        $sqladdendum .= "PRIMARY KEY (`".$name."`), ";
                    }
                    if ($field->unique === TRUE) 
                    {
                        $sqladdendum .= "UNIQUE KEY `unique_".$name."` (`".$name."`), ";
                    }
                }
            }
            $sql .= $sqladdendum;
            $sql = substr($sql,0,-2).") ENGINE = ".$this->_engine." DEFAULT CHARSET=utf8";
            return array($m->table() => $sql);
        }
        
        public function sql_for_creating_connection_table_from_sprig_model($m)
        {
            // Create Model if only string is passed
            if (is_string($m)) 
            {
                $m = Sprig::factory($m);
            }
            
            // Loop through the fields and see whether there needs to be a connection table
            $sqls = array();
            foreach ($m->fields() as $name => $field)
            {
                // There needs to be a connection table when there is a ManyToMany relation
                if (is_a($field, "Sprig_Field_ManyToMany")) 
                {
                    // Sprig has already figured out what the ->through table will be
                    //$connectionsqls 
                    $table1 = Inflector::singular($m->table());
                    $table2 = Inflector::singular(Sprig::factory($field->model)->table());
                    $sql = "CREATE TABLE IF NOT EXISTS `".$field->through."` (`id` int(11) unsigned NOT NULL auto_increment,";
                    $sql .= "`".$table1."_id` int(11) unsigned NOT NULL,";
                    $sql .= "`".$table2."_id` int(11) unsigned NOT NULL,";
                    $sql .= "PRIMARY KEY  (`id`),";
                    $sql .= "UNIQUE KEY `fk_".$table1."_id` (`".$table1."_id`,`".$table2."_id`),";
                    $sql .= "KEY `fk_".$table2."_id` (`".$table2."_id`,`".$table1."_id`)";
                    $sql .= ") ENGINE=".$this->_engine." DEFAULT CHARSET=utf8;";
                    $sqls[$field->through] = $sql;
                }
            }
            return $sqls;
        }
        
        
        
    }

?>
