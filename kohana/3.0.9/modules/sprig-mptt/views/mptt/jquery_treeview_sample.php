<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package MPTT
 * @author Mathew Davies
 * @author Kiall Mac Innes
 */?>
<link rel="stylesheet" type="text/css" href="http://view.jquery.com/trunk/plugins/treeview/jquery.treeview.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="http://view.jquery.com/trunk/plugins/treeview/jquery.treeview.js"></script>
	<ul id="tree">
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
<script type="text/javascript">
	$(document).ready(function()
	{
		$("#tree").treeview({
			animated:"medium",
			collapsed:true				
		});
	});	

</script>

