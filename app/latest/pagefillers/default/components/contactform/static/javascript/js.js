$(function(){
	wi3.makeExist("wi3.pagefiller.default.component.contacform");

	wi3.pagefiller["default"].component.contacform.submit = function(elm) {
		var field = $(elm).closest("[type=field]");
		var fieldId = field.attr("fieldid");
		// find controller
		wi3.request("pagefiller_default_component_contactform/submit", { 
			fieldid : fieldId,
			name: field.find("[name=name]").val(),
			emailaddress: field.find("[name=emailaddress]").val(),
			subject: field.find("[name=subject]").val(), 
			message: field.find("[name=message]").val() 
		});
	}
});