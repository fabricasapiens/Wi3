wi3.makeExist("wi3.pagefiller.default.simpleblogsearch");

wi3.pagefiller.default.simpleblogsearch.renderResults = function(input) {

	var bestIndex = -1;
	// get results
	var val = $(input).val().toLowerCase();
	if (val.length) {
		var results = [];
		for(index in wi3.pagefiller.default.simpleblogsearch.searchdata.pages) {
			var d = wi3.pagefiller.default.simpleblogsearch.searchdata.pages[index];
			var title = d.title.toLowerCase();
			if (title.indexOf(val) !== -1) {
				// more to the beginning is better
				var score = title.indexOf(val) / title.length;
				results.push({id:index, score:score});
			}
		}
		if (results.length) {
			results.sort(function(a,b) {
				return a.score - b.score;
			});
			bestIndex = results[0].id;
		} else {
			//
		}		
	} else {
		//
	}

	if (bestIndex != -1) {
		var result = wi3.pagefiller.default.simpleblogsearch.searchdata.pages[bestIndex];
		var a = $("<a></a>").attr("href",result.url).html(result.title);
		$(".wi3_pagefiller_default_component_simpleblogsearch_result").html(a);
		$(".wi3_pagefiller_default_component_simpleblogsearch_result").show();
	} else {
		$(".wi3_pagefiller_default_component_simpleblogsearch_result").fadeOut();
	}
}

wi3.pagefiller.default.simpleblogsearch.hideResults = function() {
	$(".wi3_pagefiller_default_component_simpleblogsearch_result").fadeOut();
}