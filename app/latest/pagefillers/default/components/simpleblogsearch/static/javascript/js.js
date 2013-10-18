wi3.makeExist("wi3.pagefiller.default.simpleblogsearch");

$(function() {
	$(".wi3_pagefiller_default_component_simpleblogsearch").each(function(index,component) {
		var currentlySelectedResult = -1;

		$(component).keyup(function(event) {
			if (event.which == 13) {
				// Go to currentlySelected element
				if (getResult(currentlySelectedResult)) {
					if (getResult(currentlySelectedResult).find("a").length) {
						document.location.href = getResult(currentlySelectedResult).find("a").first().attr("href");	
					}
				} else {
					// Pick first result, if present
					if (getResult(0)) {
						if (getResult(0).find("a").length) {
							document.location.href = getResult(0).find("a").first().attr("href");	
						}
					}
				}
			} else if (event.which == 38 || event.which == 40) {
				// Go down or up in list
				if (event.which == 40) {
					if (currentlySelectedResult < getAmountOfRenderedResults()-1) {
						currentlySelectedResult++; // down in list
					}
				} else {
					if (currentlySelectedResult > -1) {
						currentlySelectedResult--; // up in list
					}
				}
				renderSelected(currentlySelectedResult);
			} else {
				renderResults();
			}
		}).keydown(function(){
			if (event.which == 38 || event.which == 40) {
				event.preventDefault();
				event.stopPropagation();
			}
		}).blur(function() {
			$(component).next("div").find(".wi3_pagefiller_default_component_simpleblogsearch_result").fadeOut();
		});

		function getResult(index) {
			if (index < 0) {
				return false;
			}
			var e = $(component).next("div").find(".wi3_pagefiller_default_component_simpleblogsearch_result div").eq(index);
			return (e.length ? e : false);
		}

		function renderSelected(index) {
			var selected = getResult(index);
			if (selected) {
				selected.addClass("selected").siblings().removeClass("selected"); 
			} else {
				$(component).next("div").find(".wi3_pagefiller_default_component_simpleblogsearch_result div").removeClass("selected");
			}
		}

		function getAmountOfRenderedResults() {
			return $(component).next("div").find(".wi3_pagefiller_default_component_simpleblogsearch_result div").size();
		}

		// get results
		function renderResults() {
			var val = $(component).val().toLowerCase();
			if (val.length) {
				var results = [];
				for(index in wi3.pagefiller["default"].simpleblogsearch.searchdata.pages) {
					var d = wi3.pagefiller["default"].simpleblogsearch.searchdata.pages[index];
					var title = d.title.toLowerCase();
					if (title.indexOf(val) !== -1) {
						// more to the beginning is better
						var score = title.indexOf(val) / title.length;
						results.push({type:"page", id:index, score:score});
					}
				}
				for(index in wi3.pagefiller["default"].simpleblogsearch.searchdata.articles) {
					var d = wi3.pagefiller["default"].simpleblogsearch.searchdata.articles[index];
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
				$(component).next("div").find(".wi3_pagefiller_default_component_simpleblogsearch_result").html("");
				var counter = 0;
				for(var index in results) {
					counter++;
					if (counter > 5) {
						break;
					}
					if (results[index].type == "page") {
						var result = wi3.pagefiller["default"].simpleblogsearch.searchdata.pages[results[index].id];
					} else {
						var result = wi3.pagefiller["default"].simpleblogsearch.searchdata.articles[results[index].id];
					}
					var a = $("<div></div>").html("<a href='" + result.url + "'>" + result.title + "</a>");
					$(component).next("div").find(".wi3_pagefiller_default_component_simpleblogsearch_result").append(a);
				}
			} else {
				$(component).next("div").find(".wi3_pagefiller_default_component_simpleblogsearch_result").html("<div><a>No results</a></div>");
			}
			$(component).next("div").find(".wi3_pagefiller_default_component_simpleblogsearch_result").show();
		}

	});
});