<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package MPTT
 * @author Mathew Davies
 * @author Kiall Mac Innes
 */?>
<table>
	<thead>
		<td>PK</td>
		<td>Level</td>
		<td>Left</td>
		<td>Right</td>
		<td>Name</td>
	</thead>
<?php
$level = $nodes->current()->{$level_column};
$first = TRUE;

foreach ($nodes as $node)
{
	if ( ! $first)
	{
	?>
		</tr>
	<?php
	}
	?>
	<tr>
		<td><?php print $node->{$node->pk()}; ?></td>
		<td><?php print $node->{$node->level_column}; ?></td>
		<td><?php print $node->{$node->left_column}; ?></td>
		<td><?php print $node->{$node->right_column}; ?></td>
		<td>
		<?php
		$c = 1;
		while ($c < $node->{$node->level_column})
		{
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			$c++;
		}
		print 'ID: '.$node->{$node->pk()};
		?>
		</td>
	<?php
	//$level = $node->{$node->level_column};
	$first = FALSE;
}
?>
</tr>
</table>