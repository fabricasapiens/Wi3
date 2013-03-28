wi3.makeExist("wi3.pagefillers.default");

$(document).ready( function(){
    // Let all contenteditables use filteredPaste
    $("[contenteditable]").filteredPaste();
    // Make toolbar ready for use
    wi3.pagefillers.default.edittoolbar.activeEditor = $("[contenteditable='true']");
    jQuery.extend(wi3.pagefillers.default.edittoolbar.activeEditor, WysiHat.Commands);
    //$("#pagefiller_default_edittoolbar").css("width", ($(document).width()-100) + "px"); // With fixed positions, there is never an x-overflow, so width: 100% with padding works, so this line is redundant
    // Make all fields hoverable
    wi3.pagefillers.default.edittoolbar.enableFieldActions($("[type=field]"));
    // Catch all 'delete' and 'backspace' keys and disable them when they are about to delete a complete field...!
    // Also, catch some control+key combinations
    wi3.pagefillers.default.edittoolbar.controlKeyDown = false;
    // TODO: make this work with the simple e.ctrlKey
    $(document).keydown(function (e) {
        
        // Track some Control+key functions for e.g. saving (Control + s)
        if (e.which == 17)
        {
            wi3.pagefillers.default.edittoolbar.controlKeyDown = true;
        }
        if (wi3.pagefillers.default.edittoolbar.controlKeyDown && e.which == 83) // Ctrl + 's' key
        {
            e.preventDefault();
            wi3.pagefillers.default.edittoolbar.saveAllEditableBlocks();
        }
        
        // Track delete and backspace events, and prevent those events from deleting fields
        // backspace is code 8
        // delete is code 46
        if(e.which == 8 || e.which == 46)
        {
            // identify place of the cursor
            var sel = rangy.getSelection();
            var range = sel.rangeCount != 0 ? sel.getRangeAt(0) : null;
            if (range) 
            {   
                // check whether the range is within a contenteditable=true area and NOT in contenteditable=false
                var closest = $(range.startContainer).parent().closest("[contenteditable]");
                if (closest.attr("contenteditable") == "true") // the closest element that has and contenteditable should be one with contentEditable=true
                {
                    var startContainer = range.startContainer; // is always a textNode
                    var startOffset = range.startOffset;
                    // For delete, check if we are at the END of the container, and have a field after that container (so we should prevent a delete)
                    if (e.which == 46 && startOffset == $(startContainer).text().length && $(startContainer.nextSibling).attr("type") == "field")
                    {
                        e.preventDefault();
                    }
                    // For backspace, check if we are at the BEGINNING of the container, and have a field before that container (so we should prevent a delete)
                    if (e.which == 8 && startOffset == 0 && $(startContainer.previousSibling).attr("type") == "field")
                    {
                        e.preventDefault();
                    }
                }
            }
        }
    }).keyup(function (e) {
        if (e.which == 17)
        {
            wi3.pagefillers.default.edittoolbar.controlKeyDown = false;
        }
    });
});

wi3.pagefillers.default.edittoolbar = {

    getActiveEditor : function()
    {
        return wi3.pagefillers.default.edittoolbar.activeEditor;
    },
    
    showPopup : function()
    {
        $("div[type=popup]").slideDown("fast");
    },

    hidePopup : function()
    {
        $("div[type=popup]").hide();
    },
    
    saveAllEditableBlocks : function()
    {
        // insert the style-float and style-padding tags into the fields, since PHPQuery does not properly support the css() function 
        $("[type=field]").each(function(counter) {
            // For padding, we use padding-left, and assume all paddings are like that 
            // Simply using .css('padding') does not work, due to "Shorthand CSS properties (e.g. margin, background, border) are not supported" (http://api.jquery.com/css/)
            $(this).attr("style_padding", $(this).css("padding-left"));
            $(this).attr("style_float", $(this).css("float"));
            $(this).attr("style_width", this.style.width || $(this).css("width")); 
        });
        // send the complete html of the page to the server. The server will distill the editable blocks, and save them 
        if (parent && parent.wi3) {
            var usedwi3 = parent.wi3; // Do request via parent if possible, so that ajax request indicator works
        } else {
            var usedwi3 = wi3;
        }
        usedwi3.request("pagefiller_default_edittoolbar_ajax/savealleditableblocks", {pageid: $("#pagefiller_default_edittoolbar_pageid").text(), html:$("body").html()});
    },
    
    formatblock : function(blocktype)
    {
        wi3.pagefillers.default.edittoolbar.getActiveEditor().formatblockSelection(blocktype);
    },
    
    insertField : function(fieldtype)
    {
        // identify place where field should be added
        var sel = rangy.getSelection();
        var range = sel.rangeCount != 0 ? sel.getRangeAt(0) : null;
        if (range) 
        {   
            // check whether the range is within a contenteditable=true area and NOT in contenteditable=false
            var closest = $(range.startContainer).parent().closest("[contenteditable]");
            if (closest.attr("contenteditable") == "true") // the closest element that has and contenteditable should be one with contentEditable=true
            {
                wi3.pagefillers.default.edittoolbar.insertFieldRange = range;
                // Insert a field
                var request = wi3.request("pagefiller_default_edittoolbar_ajax/insertfield", { pageid: $("#pagefiller_default_edittoolbar_pageid").text(), fieldtype:fieldtype, selectiontext: range.toString() });
            }
            else
            {
               // Well, just don't do anything
            }
        }
    },

    toBase64 : function(val) {
        return $.base64.encode(val);
    },

    fromBase64 : function(baseVal) {
        return $.base64.decode(baseVal);
    },
	
	renderFieldHtml : function(fieldid, html) {
		// html is base64 encoded
        html = $.base64.decode(html);
		$("[fieldid=" + fieldid + "] [type=fieldcontent]").html(html);
		// make the hover work that enables resizing, deletion etc for child components
        wi3.pagefillers.default.edittoolbar.enableFieldActions($("[fieldid=" + fieldid + "] *[fieldid]"));
	},
    
    insertFieldHtml : function(fieldid, html, replacetype)
    {
        // html is base64 encoded
        html = $.base64.decode(html);
        if (replacetype == "insertbefore")
        {
            // Insert the html before the selection
            // Check whether the insert location is known
            if (wi3.pagefillers.default.edittoolbar.insertFieldRange.startContainer)
            {   
                // insert html at the location
                var start = $(wi3.pagefillers.default.edittoolbar.insertFieldRange.startContainer);
                var parent = start.parent();
                // Get the 'best' location to insert the field: before or after a word, not in the middle of a word
                if (wi3.pagefillers.default.edittoolbar.insertFieldRange.startOffset == 0)
                {
                    // Caret is at start of textnode, that is perfect!
                    var startbeforetext = start.text().substr(0, wi3.pagefillers.default.edittoolbar.insertFieldRange.startOffset);
                    var startaftertext = start.text().substr(wi3.pagefillers.default.edittoolbar.insertFieldRange.startOffset);
                }
                else
                {
                    var spacelocation = start.text().indexOf(" ", wi3.pagefillers.default.edittoolbar.insertFieldRange.startOffset); // Find first space after
                    if (spacelocation == -1)
                    {
                        // If there is no space after the caret position, then just go to the end of the container
                        spacelocation = start.text().length;
                    }
                    var startbeforetext = start.text().substr(0, spacelocation);
                    var startaftertext = start.text().substr(spacelocation);
                }
                
                // replace selection
                //wi3.pagefillers.default.edittoolbar.getActiveEditor().insertHTML(html);
                start.replaceWith(startbeforetext + html + startaftertext); // the startbeforetext gets a line-break or something on the end??
                
                // This next section should ideally not happen, but we can not guarantee that all child-elements are spans
                // If the parent of the field is an inline-element (<p>, <span> etc), we convert it to <div> while taking the existing metrics from the current parent
                var tagname = parent.get(0).tagName;
                // Recursively bubble to the top, until we encounter a block-level element (i.e. not P or SPAN)
                while (tagname && (tagname == "P" || tagname == "SPAN"))
                {
                    var parent = changeTagName(parent, "div");
                    tagname = parent.get(0).tagName;
                }
            }            
        }
        else if (replacetype == "insertafter")
        {
            // Insert the html after the selection
            // Check whether the insert location is known
            if (wi3.pagefillers.default.edittoolbar.insertFieldRange.endContainer)
            {   
                // insert html at the location
                var end = $(wi3.pagefillers.default.edittoolbar.insertFieldRange.endContainer);
                var parent = end.parent();
                // Get the 'best' location to insert the field: before or after a word, not in the middle of a word
                if (wi3.pagefillers.default.edittoolbar.insertFieldRange.endOffset == 0)
                {
                    // Caret is at start of textnode, that is perfect!
                    var beforetext = end.text().substr(0, wi3.pagefillers.default.edittoolbar.insertFieldRange.endOffset);
                    var aftertext = end.text().substr(wi3.pagefillers.default.edittoolbar.insertFieldRange.endOffset);
                }
                else
                {
                    var spacelocation = end.text().indexOf(" ", wi3.pagefillers.default.edittoolbar.insertFieldRange.endOffset); // Find first space after
                    if (spacelocation == -1)
                    {
                        // If there is no space after the caret position, then just go to the end of the container
                        spacelocation = end.text().length;
                    }
                    var beforetext = end.text().substr(0, spacelocation);
                    var aftertext = end.text().substr(spacelocation);
                }
                
                // replace selection
                //wi3.pagefillers.default.edittoolbar.getActiveEditor().insertHTML(html);
                end.replaceWith(beforetext + html + aftertext); // the startbeforetext gets a line-break or something on the end??
                
                // This next section should ideally not happen, but we can not guarantee that all child-elements are spans
                // If the parent of the field is an inline-element (<p>, <span> etc), we convert it to <div> while taking the existing metrics from the current parent
                var tagname = parent.get(0).tagName;
                // Recursively bubble to the top, until we encounter a block-level element (i.e. not P or SPAN)
                while (tagname && (tagname == "P" || tagname == "SPAN"))
                {
                    var parent = changeTagName(parent, "div");
                    tagname = parent.get(0).tagName;
                }
            }            
        }
        else if (replacetype == "replace")
        {
            // Replace the current selection with the new html
            
            var startoffset = wi3.pagefillers.default.edittoolbar.insertFieldRange.startOffset;
            var start = $(wi3.pagefillers.default.edittoolbar.insertFieldRange.startContainer);
            var parent = start.parent();
            // Remove old content
            wi3.pagefillers.default.edittoolbar.insertFieldRange.deleteContents();
            // Insert new content at the location
            var startbeforetext = start.text().substr(0, startoffset);
            var startaftertext = start.text().substr(startoffset);
            
            // replace selection
            start.replaceWith(startbeforetext + html + startaftertext); // the startbeforetext gets a line-break or something on the end??
            
            // This next section should ideally not happen, but we can not guarantee that all child-elements are spans
            // If the parent of the field is an inline-element (<p>, <span> etc), we convert it to <div> while taking the existing metrics from the current parent
            var tagname = parent.get(0).tagName;
            // Recursively bubble to the top, until we encounter a block-level element (i.e. not P or SPAN)
            while (tagname && (tagname == "P" || tagname == "SPAN"))
            {
                var parent = changeTagName(parent, "div");
                tagname = parent.get(0).tagName;
            }
        }
        
        // make the hover work that enables resizing, deletion etc
        wi3.pagefillers.default.edittoolbar.enableFieldActions($("[fieldid=" + fieldid + "]"));
    },
    
    enableFieldActions : function(jqueryobj)
    {

        function showFieldButtonsForField(field) {
            var fieldid = field.attr("fieldid");
            var container = $("[type=fieldbuttonscontainer][fieldid="+fieldid+"]");
            if (!container.length) {
                container = $("<div>").attr("type","fieldbuttonscontainer").attr("fieldid", fieldid);
                container.append(field.data("fieldbuttons")).hide();
                container.find("[type=fieldbuttons]").show(); // subcontainer is hidden by default, and can be shown once its container is hidden
                $("body").append(container);
                // Find the buttons inside the fieldbuttons and attach the proper actions to them 
                container.find("[type=fieldbuttons] [action=remove]").bind("click", function(event) {
                    wi3.pagefillers.default.edittoolbar.removeField(fieldid);
                });
                container.find("[type=fieldbuttons] [action=align_floatleft]").bind("click", function(event) {
                    field.css("width","").css("float", "left");
                });
                container.find("[type=fieldbuttons] [action=align_floatright]").bind("click", function(event) {
                    field.css("width","").css("float", "right");
                });
                container.find("[type=fieldbuttons] [action=align_fullwidth]").bind("click", function(event) {
                    field.css("float", "none").css("width", "100%");
                });
                container.find("[type=fieldbuttons] [action=margin_0px]").bind("click", function(event) {
                    field.css("padding", "0px");
                });
                container.find("[type=fieldbuttons] [action=margin_20px]").bind("click", function(event) {
                    field.css("padding", "20px");
                });
                container.find("[type=fieldbuttons] [action=margin_40px]").bind("click", function(event) {
                    field.css("padding", "40px");
                });
            }
            var top;
            if ($(window).scrollTop() + 80 > field.offset().top) {
                top = $(window).scrollTop() + 80;
            } else {
                top = field.offset().top;
            }
            container
                .css("position", "absolute")
                .css("z-index", 1000)
                .css("top", top)
                .css("left",field.offset().left)
                .css("width",field.outerWidth(true))
                .fadeIn();
        }

        function getFieldButtonsContainerForField(field) {
            var fieldid = $(field).attr("fieldid");
            return $("[type=fieldbuttonscontainer][fieldid="+fieldid+"]");
        }

        function hideFieldButtonsForField(field) {
            getFieldButtonsContainerForField(field).fadeOut();
        }

        // jqueryobj is an array of elements
        jqueryobj = $(jqueryobj);
        // extract the fieldbuttons and move it out of the raw HTML, into a property on the field DOM element
        jqueryobj.each(function(index,elm) {
            var fieldButtons = $(elm).find("[type=fieldbuttons]").get(0).outerHTML;
            $(elm).data("fieldbuttons", fieldButtons);
            $(elm).find("[type=fieldbuttons]").first().remove();
        });
        jqueryobj.bind("mouseenter", function(event) {
            
            // Show shadow
            $(this).css("-webkit-box-shadow", "0px 0px 10px #ccc");
            $(this).css("-mozilla-box-shadow", "0px 0px 10px #ccc");
            $(this).css("box-shadow", "0px 0px 10px #ccc");
            
            var field = $(this);

            // Show and position the fieldbuttons
            showFieldButtonsForField(field);

            // Reposition the elements on window scroll and window resize
            var updatePositionAndSize = function(event) {
                showFieldButtonsForField(field);
            };
            $(window).bind("scroll", updatePositionAndSize);
            $(window).bind("resize", updatePositionAndSize);
            $(field).bind("mouseleave", function() {
                $(window).unbind("scroll", updatePositionAndSize);
                $(window).unbind("resize", updatePositionAndSize);
            });
            
        }).bind("mouseleave", function(event) {
            var that = this;
            var hideFunction = function() {
                // Hide shadow
                $(that).css("-webkit-box-shadow", "none");
                $(that).css("-mozilla-box-shadow", "none");
                $(that).css("box-shadow", "none");
                // Hide the delete and placement buttons
                hideFieldButtonsForField($(that));
            }
            var fieldid = $(this).attr("fieldid");
            if ($(event.toElement).closest("[type=fieldbuttonscontainer][fieldid="+fieldid+"]").length) { 
                // If the mouse did move to the fieldbuttons container
                var container = $("[type=fieldbuttonscontainer][fieldid="+fieldid+"]");
                container.unbind("mouseleave");
                container.bind("mouseleave", function(e) {
                    if ($(e.toElement).closest("[type=field][fieldid="+fieldid+"]").length === 0) { 
                        // Did not move back into field
                        hideFunction();
                    }
                });
            } else {
                hideFunction();
            }
            
        }).bind("click", function(event) {
            if (event.ctrlKey === true) {
                // Pick first edit-action
                var container = getFieldButtonsContainerForField(this);
                container.find("[block=fieldactions] a").first().click();
                event.stopPropagation(); // Don't bubble clicks up the DOM tree
                event.preventDefault();
            }
        });
        // Prevent clicks on text in editableblocks
        jqueryobj.find("[type=editableblock]").bind("click", function(event) {
            event.stopPropagation();
            event.preventDefault();
        });
    },
    
    removeField : function(fieldid)
    {
        var request = wi3.request("pagefiller_default_edittoolbar_ajax/removefield", { pageid: $("#pagefiller_default_edittoolbar_pageid").text(), fieldid: fieldid, elementtext: $("[fieldid="+fieldid+"] [type=fieldcontent]").text() });
    }
};

// The style functions and changetag function 
// TODO: move this into a JQuery plugin
function changeTagName(parent, newTagName)
{
    parent = $(parent);
    // Fetch the style of the <p> or <span> element
    var parentstyles = getStyles(parent);
    var parentcomputedstyles = getComputedStyles(parent);
    if (!newTagName.length) { newTagName = "div"; }
    // replace <p> or <span> with an element newTagName (e.g. a <div>)
    parent.replaceWith("<" + newTagName + " findmeback='findmeback'>" + parent.html() + "</" + newTagName + ">"); // replace the <p> or <span> with its contents
    var wrap = $("[findmeback=findmeback]").removeAttr("findmeback");
    // Set the style from the original element. Make sure to only include those styles that are actually different from the div element.
    divstyles = getStyles(wrap);
    var style = "";
    // include the parent styles that are different
    for(index in parentstyles)
    {
        if (parentstyles[index] != divstyles[index])
        {
            style += index + ": " + parentstyles[index] + "; ";
        }
    }
    // Reset the div styles that are not found in the parent
    // The reset happens by setting the value to the computed style of the original parent
    for(index in divstyles)
    {
        if (!parentstyles[index])
        {
            style += index + ": " + parentcomputedstyles[index] + ";";
        }
    }
    wrap.attr("style", style); // set all the markup from the p/span element to the div element
    return wrap; // Return the new parent
}

function getComputedStyles(element)
{
    var styles= [];
    var element = $(element).get(0);
    // The DOM Level 2 CSS way
    if ('getComputedStyle' in window) {
        var cs= getComputedStyle(element, '');
        if (cs.length!==0)
        {
            for (var i= 0; i<cs.length; i++)
            {
                styles[cs.item(i)] = cs.getPropertyValue(cs.item(i));
            }
        }
        else // Opera workaround. Opera doesn't support `item`/`length` on CSSStyleDeclaration.
        {
            for (var k in cs)
            {
                if (cs.hasOwnProperty(k))
                {
                    styles[k] = cs[k];
                }
            }
        }
    // The IE way
    } else if ('currentStyle' in element) {
        var cs= element.currentStyle;
        for (var k in cs)
        {
            styles[k] = cs[k];
        }
    }
    return styles;
}

function getStyles(element)
{
    var element = $(element).get(0);
    var rules = []; var count = 0;
    // NOTE: it is impossible to read the 'default stylesheet' so it is best to include a reset.css to have access to the 'base' rules
    // Get all the explicit stylesheets (including <style> declaratinos) and extract the matching rules
    for (index in document.styleSheets)
    {
        localrules = [];
        if (document.styleSheets[index].cssRules)
        {
            localrules = document.styleSheets[index].cssRules;
        }
        else if (document.styleSheets[index].rules)
        {
            localrules = document.styleSheets[index].rules;
        }
        // Loop through the local rules, and add them to the rules if the rule is meant for the element under study
        for(i in localrules)
        {
            if (localrules[i].selectorText && localrules[i].selectorText.length)
            {
                var selector = localrules[i].selectorText;
                // JQuery chokes on :: in the selector, so we need to remove those...
                if (selector.indexOf("::") != -1) { continue; }
                // http://stackoverflow.com/questions/2218296/jquery-check-if-jquery-object-contains-exact-dom-element
                if ( $(selector).filter(function() { return this == element; }).length ) { // selector does match element 
                    // Loop over the rules belonging to this selector, and add them to the rules array
                    for(r=0; r < localrules[i].style.length; r++)
                    {
                        rules[localrules[i].style[r]] = localrules[i].style[localrules[i].style[r]];
                    }
                }
            }
        }
    }
    // Loop over the inline style and add those rules
    var inlinestyle = $(element).attr("style");
    if (inlinestyle && inlinestyle.length > 0) 
    { 
        var inlinerules = inlinestyle.split(";");
        for(ir in inlinerules)
        {
            if (inlinerules[ir].length > 0)
            {
                var inlinerule = inlinerules[ir].split(":");
                if (inlinerule.length == 2)
                {
                    rules[inlinerule[0]] = inlinerule[1];
                }
            }
        }
    }
    return rules;
}
