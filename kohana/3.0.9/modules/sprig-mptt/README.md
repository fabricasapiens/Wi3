# Sprig_MPTT

A Modified Preorder Tree Traversal extension for [Sprig](http://github.com/shadowhand/sprig).

This extension is largely based on the [Kohana 2.x](http://www.kohanaphp.com) 
[ORM_MPTT](http://dev.kohanaphp.com/projects/mptt) module developed by 
Mathew Davies and Kiall Mac Innes. All the hardwork was done by them!

### Differences From ORM_MPTT

The vast majority of functionality is the same, including the same function names etc.

The only major change externally is that new_scope() has been renamed to insert_as_new_root() to 
keep things more consistent and to work better with Sprig's validation.

Also, as all fields have to be specified in Sprig, the four MPTT columns are defined by Sprig_Field_MPTT_* objects. They are optional though, with Sprig_MPTT automatically creating default MPTT fields if none are specified (see below).

There may be other minor differences or bugs introduced. I'll try to fix them or point them out as I find them.

## Quick Start

### Defining a Model

To create a Sprig_MPTT model, you need to create a normal Sprig model but extend Sprig_MPTT instead of Sprig.

Your table must have the four MPTT columns (left, right, level and scope) since there are reserved word clashes in most database systems, the default and recommended naming of these columns is `lft`, `rgt`, `lvl` and `scope` respectively. All four columns are integers.

If the columns in your database are named as suggested, you do not need to manually define the fields in your `_init()` method as Sprig_MPTT will create them for you. If you need to change the name of any of these columns you can do so by specifying them as normal in your `_init` method. Example:

	class Model_Example extends Sprig_MPTT
	{
		protected function _init()
		{
			$this->_fields += array(
				'id' => new Sprig_Field_Auto,
				'name' => new Sprig_Field_Char,
				'my_left' => new Sprig_Field_MPTT_Left,
				'my_right' => new Sprig_Field_MPTT_Right,
				'my_level' => new Sprig_Field_MPTT_Level,
				'my_scope' => new Sprig_Field_MPTT_Scope,
			);
		}
	}

You only need to define the MPTT fields for which the column name differs from the defaults above, the rest are created automatically.

### Setting up a Tree

The major difference between Sprig and Sprig_MPTT models is that you can no longer use the create() method. This is to stop inserts invalidating the MPTT tree.

To create a new MPTT record, you need to use one of the insert_* methods.

Before you create any other records in a tree, you need to create the root record for the tree. This can be done like:

	// Get the root node for the scope 1
	$root = Sprig::factory('test')->root(1);
	
	// If the root node isn't loaded, we must create it before we can do anything else with the tree in scope 1
	if ( ! $root->loaded())
	{
		$root->name = 'Root Node'; // All object properties which are required must be specified otherwise Validation will fail
		
		try
		{
			$root->insert_as_new_root(1);
		}
		catch (Validate_Exception $e)
		{
			// Handle the bad data
		}
	}
	
After that, you can use the scope tree with any of the other methods in the class.

### Inserting Nodes

Nodes can be inserted (created) using:

	$model->insert_as_first_child($target);
	$model->insert_as_last_child($target);
	$model->insert_as_next_sibling($target);
	$model->insert_as_prev_sibling($target);

Where `$target` is either another (loaded) Sprig_MPTT object or the primary key value (id) of one. They do exactly what you would expect.

### Moving a node

Similarly, exisiting nodes can be moved in the tree using:

	$model->move_to_first_child($target);
	$model->move_to_last_child($target);
	$model->move_to_next_sibling($target);
	$model->move_to_prev_sibling($target);

These methods will return false if you try to move a node into itself or one of it's descendants.

### Other functions

Deleting a node is the same as in Sprig. As is pretty much everything else.

### Accessing Tree Relationships

A model's related nodes can be accessed using:

	$model->parent;		// returns Sprig object for direct parent
	$model->parents;	// returns DB result for all ancestors
	$model->descendants;	// returns DB result containting all descendants
	$model->children;	// returns DB result containing direct children
	$model->first_child;	// returns Sprig object for first child
	$model->last_child;	// returns Sprig object for last child
	$model->siblings;	// returns DB result containing all sibling nodes
	$model->leaves;		// returns DB result containing all children who have no further children
	$model->root;		// retruns root node of current scope tree

To specify ordering or whether or not to include the current object in the result, use the correspondingly named methods `parents()`, `children()` etc.

### Printing the Tree

`select_list()` has been extended to support indenting as in ORM_MPTT

You may aslo use the render_descendants() or render_children() methods along to render HTML tree representations. Four example views copied from ORM_MPTT are included and demonstrate simple `<ul>` and `<table>` based lists as well as simple jQuery and YUI tree views.

## TODO

 - There are some known limitations such as you can only use an integer as a scope -- this may or may not warrant further work
 - Fix the any bugs that are found!
	
	
	
	
	
	
	
	
	
