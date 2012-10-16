<?php

	$page = Wi3::inst()->sitearea->page;
    $site = Wi3::inst()->sitearea->site;

	// Dependencies
	Wi3::inst()->plugins->load("plugin_jquery_wi3");

	$this->css("style.css");

	// Publish an array as JS object on the frontend
	$results = Array("pages"=>Array());
	foreach($pages as $index => $page) {
		$results["pages"][] = Array(
			"title" => $page->longtitle,
			"url" => Wi3::instance()->urlof->page($page)
		);
	}

	$this->javascriptObject("wi3.pagefiller.default.simpleblogsearch.searchdata",$results);

	$this->javascript("js.js");

?>

<input style='border-radius: 4px; padding: 4px; font-height: 1.5em;' class='wi3_pagefiller_default_component_simpleblogsearch' onblur='wi3.pagefiller.default.simpleblogsearch.hideResults();' placeholder='zoeken' onKeyUp='wi3.pagefiller.default.simpleblogsearch.renderResults(this);'></input>
<div style='background: #fff; max-width: 200px;'>
	<div class='wi3_pagefiller_default_component_simpleblogsearch_result'>
		...
	</div>
</div>