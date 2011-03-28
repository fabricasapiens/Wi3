<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package MPTT
 * @author Mathew Davies
 * @author Kiall Mac Innes
 */?>
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