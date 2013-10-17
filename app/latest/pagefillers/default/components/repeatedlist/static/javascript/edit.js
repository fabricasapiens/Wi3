wi3.makeExist("wi3.pagefillers.default.components.repeatedlist");

wi3.pagefillers.default.components.repeatedlist.addAfter = function(elm, index) {
	elm = $(elm);
	var parentField = elm.closest("div[type=field]");
	var parentFieldId = parentField.attr("fieldid");
	// Server request
	wi3.request("pagefiller_default_component_repeatedlist/addafter", { fieldid: parentFieldId, index: index });
};

wi3.pagefillers.default.components.repeatedlist.addAtTop = function(elm) {
	elm = $(elm);
	var parentField = elm.closest("div[type=field]");
	var parentFieldId = parentField.attr("fieldid");
	// Server request
	wi3.request("pagefiller_default_component_repeatedlist/addattop", { fieldid: parentFieldId });
};

wi3.pagefillers.default.components.repeatedlist.remove = function(elm, index) {
	elm = $(elm);
	var parentField = elm.closest("div[type=field]");
	var parentFieldId = parentField.attr("fieldid");
	// Server request
	wi3.request("pagefiller_default_component_repeatedlist/remove", { fieldid: parentFieldId, index: index });
};