//-------------------------------------------------------------
// Executes when full page is loaded (and as such, images etc are loaded as well)
//-------------------------------------------------------------
jQuery( function($) {
	// Add the fancybox ability to the 'head' element, which can never be clicked from withing a webpage
	// Thus, it can only be called with $("head").click()
	currentheight = ($(parent.document).find("iframe").offset() != null ? $(parent.window).height() - $(parent.document).find("iframe").offset().top - 3 : $(window).height()) - ($.fn.fancybox.defaults.margin * 2);
	$("head").fancybox({
        hideOnContentClick: false,
	    type: "html",
	    autoDimensions: false,
	    scrolling: "auto",
	    width: 400,
	    //height: currentheight,
	    content: function() { return wi3.popup.content; } //make it a function so that the up-to-date content is always showed, and not the initial content when running this sentence in init
    });
});


if (!wi3 || typeof(wi3) != "object") { var wi3 = {}; }

wi3.popup = {
    content : "",
    show : function(content) {
        if ($.type(content) == "string") {
            wi3.popup.content = content;
        }
        $("head").click(); //so that Fancybox is activated
    },
    getDOM : function() {
        return $("#fancybox-inner").get(0);
    }
}

wi3.request = function(controller, args) {
	//tell user request is pending...
	//get amount of currently executing requests
	var amount = 0;
	if (!args) { args = {}; }
	//possibility to make every request uncachable
	//var myDate = new Date();
    //var timestamp = myDate.getTime();
	$.post(wi3.urlof.controllerroot+controller, args,
	  function(data){

		/* there are three categories here, handled in order:
		data.scriptsbefore, which is an object with javascript that will be executed before everything else
		data.dom, which will contain an object with a few different types of dom-editing:
			- remove (removes a certain html element)
			- fill (fill an html element with some content)
			- copy (copy html element to another html element)
			- append (append some content to an html element)
			- prepend (append some content to an html element)
			these types contain objects with jquery selectors along with a parameter, depending on the type of action you choose
			(for example the destination div in the case of copy)
		data.responses, which is an object with a few key-value pairs
		data.scriptsafter, which is an object javascript that will be executed after everything else
		data.alert, which will alert a message in Purr style.
		*/

		for(var index in data.scriptsbefore) {
			try { eval(data.scriptsbefore[index]); } catch(e) {}
		}

		for(var type in data.dom) {
			if (type == "remove" || type == "delete") {
				for(var selector in data.dom[type]) {
					$(data.dom[type][selector]).remove();
				}
			} else if (type == "fill") {
				for(var selector in data.dom[type]) {
					$(selector).html(data.dom[type][selector]);
				}
			} else if (type == "fill_withfade") {
				for(var selector in data.dom[type]) {
					$(selector).fadeOut().html(data.dom[type][selector]).fadeIn();
				}
			} else if (type == "replace") {
			    for(var selector in data.dom[type]) {
				    $(selector).replaceWith(data.dom[type][selector]);
			    }
			} else if (type == "copy") {
				for(var selector in data.dom[type]) {
					$(selector).replaceWith($(data.dom[type][selector]).html());
				}
			} else if (type == "append") {
				for(var selector in data.dom[type]) {
					$(selector).append(data.dom[type][selector]);
				}
			} else if (type == "prepend") {
				for(var selector in data.dom[type]) {
					$(selector).prepend(data.dom[type][selector]);
				}
			}
		}

		for(var index in data.scriptsafter) {
            try { eval(data.scriptsafter[index]); } catch(e) { }
		}

		if (data.alert) {
			if ($("#wi3_notification_top").length) {
			    adminarea.alert(data.alert);
			} else if (parent && parent.adminarea) { // If there is a parent, assume it has the notification area...
				parent.adminarea.alert(data.alert);
			} else {
			    alert(data.alert);
			}
		}

	  }
	  , "json"
	 );
};

wi3.tree = {
    simpleTreeCollection : {},
    currentTree : function() {
        return wi3.tree.simpleTreeCollection.get(0);
    }
}

wi3.editing = {

}

wi3.dateNow = function() {
    var today = new Date();
    var datestring = "" + today.getFullYear() +
    (today.getMonth() < 9 ? "0"+(today.getMonth()+1) : today.getMonth()) +
    (today.getDate() < 10 ? "0"+today.getDate() : today.getDate()) +
    (today.getHours() < 10 ? "0" + today.getHours() : today.getHours()) +
    (today.getMinutes() < 10 ? "0" + today.getMinutes() : today.getMinutes()) +
    (today.getSeconds() < 10 ? "0" + today.getSeconds() : today.getSeconds()) + "" +
    (today.getMilliseconds() < 10 ? "0" + today.getMilliseconds() : today.getMilliseconds());
    return datestring;
}

//this function makes sure a certain object (like wi3.some1.some2.some3) exists by creating the objects from left to right, if they do not exist yet
wi3.makeExist = function(dotstring) {
    var parts = dotstring.split(".");
    var workerstring = "";
    for(part in parts) {
        workerstring += (workerstring.length > 0 ? "." : "") + parts[part];
        //if this is not an object, create it
        if (typeof(eval(workerstring)) != "object") {
            eval(workerstring + " = { };");
        }
    }
}
