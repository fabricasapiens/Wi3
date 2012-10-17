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
				results.push({type:"page", id:index, score:score});
			}
		}
		for(index in wi3.pagefiller.default.simpleblogsearch.searchdata.articles) {
			var d = wi3.pagefiller.default.simpleblogsearch.searchdata.articles[index];
			var title = d.title.toLowerCase();
			if (title.indexOf(val) !== -1) {
				// more to the beginning is better
				var score = title.indexOf(val) / title.length;
				results.push({type:"article",id:index, score:score});
			} else {
				// Match on content
				var text = d.text.toLowerCase();
				if (text.indexOf(val) != -1) {
					var score = text.indexOf(val) / text.length;
					results.push({type:"article",id:index, score:score});
				}
			}
		}
		if (results.length) {
			results.sort(function(a,b) {
				return a.score - b.score;
			});
		}
	} else {
		//
	}

	if (results && results.length) {
		$(".wi3_pagefiller_default_component_simpleblogsearch_result").html("");
		var counter = 0;
		for(var index in results) {
			counter++;
			if (counter > 5) {
				break;
			}
			if (results[index].type == "page") {
				var result = wi3.pagefiller.default.simpleblogsearch.searchdata.pages[results[index].id];
			} else {
				var result = wi3.pagefiller.default.simpleblogsearch.searchdata.articles[results[index].id];
			}
			var a = $("<div></div>").html("<a href='" + result.url + "'>" + result.title + "</a>");
			$(".wi3_pagefiller_default_component_simpleblogsearch_result").append(a);
		}
		$(".wi3_pagefiller_default_component_simpleblogsearch_result").show();
	} else {
		$(".wi3_pagefiller_default_component_simpleblogsearch_result").fadeOut();
	}
}

wi3.pagefiller.default.simpleblogsearch.hideResults = function() {
	$(".wi3_pagefiller_default_component_simpleblogsearch_result").fadeOut();
}