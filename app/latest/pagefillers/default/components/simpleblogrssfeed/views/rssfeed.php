<?php
	echo '<?xml version="1.0"?>';
?>
<rss version="2.0">

<channel>

	<title>RSS from <?php echo Wi3::inst()->urlof->site; ?></title>
	<link><?php echo Wi3::inst()->urlof->site; ?></link>
	<description>RSS feed from <?php echo Wi3::inst()->urlof->site; ?></description>

	<?php

		foreach($articles as $article) {
			echo "<item>";
				echo "<title>" . $article->title . "</title>";
				echo "<link>" . $article->pageurl . "</link>";
				echo "<description>" . $article->summary . "</description>";
			echo "</item>";
		}

	?>

</channel>

</rss>