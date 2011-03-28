<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package MPTT
 * @author Mathew Davies
 * @author Kiall Mac Innes
 */?>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/treeview/assets/skins/sam/treeview.css" />
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/treeview/treeview-min.js"></script>
<div id="tree_div">
	<ul>
	<?php
	$level = $nodes->current()->{$level_column};
	$first = TRUE;
	foreach ($nodes as $node)
	{
		if ($node->{$level_column} > $level)
		{
		?>
			<ul>
		<?php
		}
		else if ($node->{$level_column} < $level)
		{
		?>
			</ul>
			</li>
		<?php
		}
		else if ( ! $first)
		{
		?>
			</li>
		<?php
		}
		?>
		<li>ID: <?php print $node->{$node->pk()}; ?>
		
		<?php
		$level = $node->{$level_column};
		$first = FALSE;
	}
	?>
	</li>
	</ul>
</div>
<script type="text/javascript">
var tree;
(function() {
	function treeInit() { 
		tree = new YAHOO.widget.TreeView(document.getElementById("tree_div"));
		tree.render(); 
	}
	YAHOO.util.Event.onDOMReady(treeInit);
})();
</script>