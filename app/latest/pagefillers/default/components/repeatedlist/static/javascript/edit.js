wi3.makeExist("wi3.pagefillers.default.components.repeatedlist");

wi3.pagefillers.default.components.repeatedlist.increaseAmount = function(elm) {
	elm = $(elm);
	var parentField = elm.closest("div[type=field]");
	var parentFieldId = parentField.attr("fieldid");
	// Server request
	wi3.request("pagefiller_default_component_repeatedlist/increaseamount", { fieldid: parentFieldId });
};

wi3.pagefillers.default.components.repeatedlist.decreaseAmount = function(elm) {
	elm = $(elm);
	var parentField = elm.closest("div[type=field]");
	var parentFieldId = parentField.attr("fieldid");
	// Server request
	wi3.request("pagefiller_default_component_repeatedlist/decreaseamount", { fieldid: parentFieldId });
};