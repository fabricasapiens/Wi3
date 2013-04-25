<?php
	echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<rss version="2.0">

<channel>

	<title>RSS from <?php echo Wi3::inst()->urlof->site; ?></title>
	<link><?php echo Wi3::inst()->urlof->site; ?></link>
	<description>RSS feed from <?php echo Wi3::inst()->urlof->site; ?></description>

	<?php

		foreach($articles as $article) {
			echo "<item>";
				echo "<title><![CDATA[" . $article->title . "]]></title>";
				echo "<link><![CDATA[" . $article->pageurl . "]]></link>";
				echo "<guid><![CDATA[" . $article->pageurl . "]]></guid>";
				echo "<description><![CDATA[" . $article->summary . "]]></description>";
			echo "</item>";
		}

	?>

</channel>

</rss>