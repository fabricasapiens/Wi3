/*
* jQuery SimpleTree Drag&Drop plugin
* Update on 22th May 2008
* Version 0.3
*
* Licensed under BSD <http://en.wikipedia.org/wiki/BSD_License>
* Copyright (c) 2008, Peter Panov <panov@elcat.kg>, IKEEN Group http://www.ikeen.com
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*     * Redistributions of source code must retain the above copyright
*       notice, this list of conditions and the following disclaimer.
*     * Redistributions in binary form must reproduce the above copyright
*       notice, this list of conditions and the following disclaimer in the
*       documentation and/or other materials provided with the distribution.
*     * Neither the name of the Peter Panov, IKEEN Group nor the
*       names of its contributors may be used to endorse or promote products
*       derived from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY Peter Panov, IKEEN Group ``AS IS'' AND ANY
* EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL Peter Panov, IKEEN Group BE LIABLE FOR ANY
* DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
* ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/


$.fn.simpleTree = function(opt){
	return this.each(function(){
		var TREE = this;
		var ROOT = $('.root',this);
		var mousePressed = false;
		var mouseMoved = false;
		var dragCheck_passed = false;
		var dragCheck_originalLocation = { x: 0, y: 0};
		var dragMoveType = false;
		var dragNode_destination = false;
		var dragNode_source = false;
		var dragDropTimer = false;
		var ajaxCache = Array();

		TREE.option = {
			drag:		true,
			animate:	false,
			dragOffset: 22, //added by Willem Mulder on how 'sensitive' the dragging is. With default, one needs to drag the element 22px before the element is really in 'dragmode'
			autoclose:	false,
			speed:		'fast',
			afterAjax:	false,
			afterMove:	false,
			afterDrag:  false,
			whileDrag:  false,
			afterClick:	false,
			afterDblClick:	false,
			// added by Erik Dohmen (2BinBusiness.nl) to make context menu cliks available
			afterContextMenu:	false,
			docToFolderConvert: true
		};
		TREE.option = $.extend(TREE.option,opt);
		$.extend(this, {getSelected: function(){
			return $('span.active', this).parent();
		}});
		TREE.closeNearby = function(obj)
		{
			$(obj).siblings().filter('.folder-open, .folder-open-last').each(function(){
				var childUl = $('>ul',this);
				var className = this.className;
				this.className = className.replace('open','close');
				if(TREE.option.animate)
				{
					childUl.animate({height:"toggle"},TREE.option.speed);
				}else{
					childUl.hide();
				}
			});
		};
		TREE.nodeToggle = function(obj)
		{
			var childUl = $('>ul',obj);
			if(childUl.is(':visible')){
				obj.className = obj.className.replace('open','close');

				if(TREE.option.animate)
				{
					childUl.animate({height:"toggle"},TREE.option.speed);
				}else{
					childUl.hide();
				}
			}else{
				obj.className = obj.className.replace('close','open');
				if(TREE.option.animate)
				{
					childUl.animate({height:"toggle"},TREE.option.speed, function(){
						if(TREE.option.autoclose)TREE.closeNearby(obj);
						if(childUl.is('.ajax'))TREE.setAjaxNodes(childUl, obj.id);
					});
				}else{
					childUl.show();
					if(TREE.option.autoclose)TREE.closeNearby(obj);
					if(childUl.is('.ajax'))TREE.setAjaxNodes(childUl, obj.id);
				}
			}
		};
		TREE.setAjaxNodes = function(node, parentId, callback)
		{
			if($.inArray(parentId,ajaxCache) == -1){
				ajaxCache[ajaxCache.length]=parentId;
				var url = $.trim($('>li', node).text());
				if(url && url.indexOf('url:'))
				{
					url=$.trim(url.replace(/.*\{url:(.*)\}/i ,'$1'));
					$.ajax({
						type: "GET",
						url: url,
						contentType:'html',
						cache:false,
						success: function(responce){
							node.removeAttr('class');
							node.html(responce);
							$.extend(node,{url:url});
							TREE.setTreeNodes(node, true);
							if(typeof TREE.option.afterAjax == 'function')
							{
								TREE.option.afterAjax(node);
							}
							if(typeof callback == 'function')
							{
								callback(node);
							}
						}
					});
				}
				
			}
		};
		TREE.setTreeNodes = function(obj, useParent){
			// changed some stuff here to account for JQuery 1.4 that cannot make a parent() of a 'virtual' obj (through e.g. $("<li>");)
			// We assume that useParent is only used when a new node is added through addNode
			// var spans = (useParent ? $('span', obj) : );
			$('li>span', obj).addClass('text')
			.bind('selectstart', function() {
				return false;
			}).click(function(){
				$('.active',TREE).attr('class','text');
				if(this.className=='text')
				{
					this.className='active';
				}
				if(typeof TREE.option.afterClick == 'function')
				{
					TREE.option.afterClick($(this).parent());
				}
				return false;
			}).dblclick(function(){
				mousePressed = false;
				TREE.nodeToggle($(this).parent().get(0));
				if(typeof TREE.option.afterDblClick == 'function')
				{
					TREE.option.afterDblClick($(this).parent());
				}
				return false;
				// added by Erik Dohmen (2BinBusiness.nl) to make context menu actions
				// available
			}).bind("contextmenu",function(){
				$('.active',TREE).attr('class','text');
				if(this.className=='text')
				{
					this.className='active';
				}
				if(typeof TREE.option.afterContextMenu == 'function')
				{
					TREE.option.afterContextMenu($(this).parent());
				}
				return false;
			}).bind("mouseover", function(){
			    if(typeof TREE.option.whileHover == 'function')
				{
					TREE.option.whileHover($(this).parent());
				}
				return false;
			}).bind("mouseout", function(){
			    if(typeof TREE.option.afterHover == 'function')
				{
					TREE.option.afterHover($(this).parent());
				}
				return false;
			}).mousedown(function(event){
				mousePressed = true;
				cloneNode = $(this).parent().clone();
				var LI = $(this).parent();
				if(TREE.option.drag)
				{
					$('>ul', cloneNode).hide();
					$('body').append('<div id="drag_container"><ul></ul></div>');
					$('#drag_container').hide().css({opacity:'0.8'});
					$('#drag_container >ul').html(cloneNode);
					$("<img>").attr({id	: "tree_plus",src	: "images/plus.gif"}).css({width: "7px",display: "block",position: "absolute",left	: "5px",top: "5px", display:'none'}).appendTo("body");
					//reset dragCheck
					dragCheck_passed = false;
					dragCheck_originalLocation = {x: event.pageX, y: event.pageY};
					$(document).bind("mousemove", {LI:LI}, TREE.dragCheck).bind("mouseup",{LI:LI},TREE.dragEnd); //Added 1) offset-check with dragStart and 2) LI data to dragEnd!
				}
				return false;
			}).mouseup(function(){
				if(mousePressed && mouseMoved && dragNode_source)
				{
					TREE.moveNodeToFolder($(this).parent());
				}
				TREE.eventDestroy();
			});
			$('li', obj).each(function(i){
				var className = this.className;
				var open = false;
				var cloneNode=false;
				var LI = this;
				var childNode = $('>ul',this);
				if(childNode.size()>0 || $(this).hasClass('permanent-folder')){
				    if ($(this).hasClass('permanent-folder')) {
					    var setClassName = 'permanent-folder folder'; // Set folder as last, since it can be appended with e.g. -last
				    } else {
				        var setClassName = 'folder'; 
				    }
					if (childNode.size()>0) {
					    if(className && className.indexOf('open')>=0){
						    setClassName=setClassName+'-open';
						    open=true;
					    }else{
						    setClassName=setClassName+'-close';
					    }
				    }
					this.className = setClassName + ($(this).is(':last-child')? '-last':'');

					if(!open || className.indexOf('ajax')>=0)childNode.hide();

					TREE.setTrigger(this);
				}else{
					var setClassName = 'doc';
					this.className = setClassName + ($(this).is(':last-child')? '-last':'');
				}
			}).before('<li class="line">&nbsp;</li>')
			.filter(':last-child').after('<li class="line-last"></li>');
			TREE.setEventLine($('.line, .line-last', obj));
		};
		TREE.setTrigger = function(node){
		    //edit: made a <div> appear instead of an <img>. This prevents a gray box from showing up in Chrome
			$('>span',node).before('<div class="trigger" />');
			var trigger = $('>.trigger', node);
			trigger.click(function(event){
				TREE.nodeToggle(node);
			});
			if(!$.browser.msie)
			{
				trigger.css('float','left');
			}
		};
		TREE.dragCheck = function(event){
		    if(dragCheck_passed) {
		        //element has already passed this check, so let it be dragged
		        TREE.dragStart(event);
		    } else {
		        //otherwise
		        if(mousePressed)
			    {
			       //check if it has been dragged more than 10px 
			       var xdistance = event.pageX - dragCheck_originalLocation.x;
			       var ydistance = event.pageY - dragCheck_originalLocation.y;
			       var distance = Math.sqrt(xdistance*xdistance/2 + ydistance*ydistance); //one should drag the node quite far on the x axis to make it pass the check. The y axix is more sensitive
			       if (distance > TREE.option.dragOffset) {
			            dragCheck_passed = true;
			            TREE.dragStart(event);
			       }
			    }
			}
		};
		TREE.dragStart = function(event){
			var LI = $(event.data.LI);
			if(mousePressed)
			{
			    //added this callback!
			    if(typeof TREE.option.whileDrag == 'function')
				{
					TREE.option.whileDrag(LI, $(event.target)); //dragging LI over target
				}//end added option
				mouseMoved = true;
				if(dragDropTimer) clearTimeout(dragDropTimer);
				if($('#drag_container:not(:visible)')){
					$('#drag_container').show();
					LI.prev('.line').hide();
					dragNode_source = LI;
				}
				$('#drag_container').css({position:'absolute', "left" : (event.pageX + 5), "top": (event.pageY + 15) });
				if(LI.is(':visible'))
				{
				    LI.hide();
				}
				var temp_move = false;
				if(event.target.tagName.toLowerCase()=='span' && $.inArray(event.target.className, Array('text','active','trigger'))!= -1)
				{
					var parent = event.target.parentNode;
					var offs = $(parent).offset({scroll:false});
					var screenScroll = {x : (offs.left - 3),y : event.pageY - offs.top};
					var isrc = $("#tree_plus").attr('src');
					var ajaxChildSize = $('>ul.ajax',parent).size();
					var ajaxChild = $('>ul.ajax',parent);
					screenScroll.x += 19;
					screenScroll.y = event.pageY - screenScroll.y + 5;

					if(parent.className.indexOf('folder-close')>=0 && ajaxChildSize==0)
					{
						if(isrc.indexOf('minus')!=-1)$("#tree_plus").attr('src','images/plus.gif');
						$("#tree_plus").css({"left": screenScroll.x, "top": screenScroll.y}).show();
						dragDropTimer = setTimeout(function(){
							parent.className = parent.className.replace('close','open');
							$('>ul',parent).show();
						}, 700);
					}else if(parent.className.indexOf('folder')>=0 && ajaxChildSize==0){
						if(isrc.indexOf('minus')!=-1)$("#tree_plus").attr('src','images/plus.gif');
						$("#tree_plus").css({"left": screenScroll.x, "top": screenScroll.y}).show();
					}else if(parent.className.indexOf('folder-close')>=0 && ajaxChildSize>0)
					{
						mouseMoved = false;
						$("#tree_plus").attr('src','images/minus.gif');
						$("#tree_plus").css({"left": screenScroll.x, "top": screenScroll.y}).show();

						$('>ul',parent).show();
						/*
							Thanks for the idea of Erik Dohmen
						*/
						TREE.setAjaxNodes(ajaxChild,parent.id, function(){
							parent.className = parent.className.replace('close','open');
							mouseMoved = true;
							$("#tree_plus").attr('src','images/plus.gif');
							$("#tree_plus").css({"left": screenScroll.x, "top": screenScroll.y}).show();
						});

					}else{
						if(TREE.option.docToFolderConvert)
						{
							$("#tree_plus").css({"left": screenScroll.x, "top": screenScroll.y}).show();
						}else{
							$("#tree_plus").hide();
						}
					}
				}else{
					$("#tree_plus").hide();
				}
				return false;
			}
			return true;
		};
		TREE.dragEnd = function(event){
			if(dragDropTimer) clearTimeout(dragDropTimer);
			//added this callback!
		    if(typeof TREE.option.afterDrag == 'function')
			{
				TREE.option.afterDrag(event.data.LI, $(event.target)); //dragged LI on target
			}//end added option
			TREE.eventDestroy();
		};
		TREE.setEventLine = function(obj){
			obj.mouseover(function(){
				if(this.className.indexOf('over')<0 && mousePressed && mouseMoved)
				{
					this.className = this.className.replace('line','line-over');
				}
			}).mouseout(function(){
				if(this.className.indexOf('over')>=0)
				{
					this.className = this.className.replace('-over','');
				}
			}).mouseup(function(){
				if(mousePressed && dragNode_source && mouseMoved)
				{
					dragNode_destination = $(this).parents('li:first');
					TREE.moveNodeToLine(this);
					TREE.eventDestroy();
				}
			});
		};
		TREE.checkNodeIsLast = function(node)
		{
		    //dragNode_source is the full tree of nodes that is moved
		    //'node' is actually dragNode_source[0], so the first node (root) of the moved tree
			if(node.className.indexOf('last')>=0)
			{
				var prev_source = dragNode_source.prev().prev();
				if(prev_source.size()>0)
				{
					prev_source[0].className+='-last';
				}
				node.className = node.className.replace('-last','');
			}
		};
		TREE.checkLineIsLast = function(line)
		{
			if(line.className.indexOf('last')>=0)
			{
				var prev = $(line).prev();
				if(prev.size()>0)
				{
					prev[0].className = prev[0].className.replace('-last','');
				}
				dragNode_source[0].className+='-last';
			}
		};
		TREE.eventDestroy = function()
		{
			// only remove the 'mousemove' event for TREE.dragCheck. Other mousemove binds will thus still work
			$(document).unbind('mousemove',  TREE.dragCheck).unbind('mouseup').unbind('mousedown');
			$('#drag_container, #tree_plus').remove();
			if(dragNode_source)
			{
				$(dragNode_source).show().prev('.line').show();
			}
			dragNode_destination = dragNode_source = mousePressed = mouseMoved = false;
			//ajaxCache = Array();
		};
		TREE.addNodeContainer = function(node){
		    if ($(node).hasClass("permanent-folder")) {
		        // Permanent-folder. Add the folder class and leave permanent-folder intact
		        $(node).addClass("folder-open");
		    } else {
		        // Document. Change the doc class to a folder-open class, if allowed to do so
		        if (TREE.option.docToFolderConvert) {
    			    node[0].className = node[0].className.replace('doc','folder-open');
			    } else {
			        // This should never happen, since the drop of a node on a doc is not allowed when docToFolderConvert == false
			    }
		    }
			node.append('<ul><li class="line-last"></li></ul>');
			TREE.setTrigger(node[0]);
			TREE.setEventLine($('.line, .line-last', node));
		};
		TREE.removeNodeContainer = function(node){
			$('>ul', node).remove();
			$('img', node).remove();
			$(node).removeClass("folder-open").removeClass("folder-close");
			if ($(node).hasClass("permanent-folder")) {
			    // Just leave as is
			    $(node).addClass("folder");
			} else {
			    // If folder can be converted to doc, do so
			    if (TREE.option.docToFolderConvert) {
			        $(node).addClass("doc");
		        } else {
        			$(node).addClass("folder");
    			}
			    
		    }
		};
		TREE.moveNodeToFolder = function(node)
		{
		    //console.log(node[0].className);
			if(!TREE.option.docToFolderConvert && node[0].className.indexOf('doc')!=-1)
			{
				return true;
			} else if($(node).hasClass('permanent-folder') || 
			         (TREE.option.docToFolderConvert && node[0].className.indexOf('doc')!=-1)) {
				TREE.addNodeContainer(node);
			}
			TREE.checkNodeIsLast(dragNode_source[0]);
			// Last-line is the object before which our object will be dropped. All folders have such a last-line
			var lastLine = $('>ul >.line-last', node);
			if(lastLine.size()>0)
			{
				TREE.moveNodeToLine(lastLine[0], true);
			}
		};
		TREE.moveNodeToLine = function(node, nodetofolder){ //edited to include a message if originally the node was dropped on a folder (that is: on another node)
		    //'node' is the line where the moved tree (dragNode_source) is dropped upon
		    if($(node).prev().get(0) != dragNode_source[0]) { // Check if the element is not placed on the spot it came from. In that case, do not do these checks, as they corrupt last-doc class
			   TREE.checkNodeIsLast(dragNode_source[0]);
			   TREE.checkLineIsLast(node);
			}
			var parent = $(dragNode_source).parents('li:first');
			var line = $(dragNode_source).prev('.line');
			// Drop dragNode before targetNode and take its previous line with him
			$(node).before(dragNode_source);
			$(dragNode_source).before(line);
			//console.log(dragNode_source);
			node.className = node.className.replace('-over','');
			// Check how many nodes remain in the original location of dragNode
			var nodeSize = $('>ul >li', parent).not('.line, .line-last').filter(':visible').size();
			if(nodeSize==0)
			{
				TREE.removeNodeContainer(parent);
			} 

			if($('span:first',dragNode_source).attr('class')=='text')
			{
				$('.active',TREE).attr('class','text');
				$('span:first',dragNode_source).attr('class','active');
			}

			if(typeof(TREE.option.afterMove) == 'function')
			{
				var pos = $(dragNode_source).prevAll(':not(.line)').size();
				//TREE.option.afterMove($(node).parents('li:first'), $(dragNode_source), pos);
				if (nodetofolder == true) { //this condition is added!
				    //get the parent li of the destination line. That parent is the destination node
				    TREE.option.afterMove($(node).parent().closest('li'), $(dragNode_source), pos); 
				} else {
				    //just return the line where the node was dropped upon
				    TREE.option.afterMove($(node).closest('li'), $(dragNode_source), pos);
				}
			}
		};

		TREE.addNode = function(id, text, callback)
		{
		    // Was: $('<li class="doc" id="'+id+'"><span>'+text+'</span></li>'); 
		    // TREE.setTreeNodes(temp_node, true);			
		    // Changed into below for JQuery 1.4 compitability
			// var temp_node = $("<span>").html('<li class="doc" id="'+id+'"><span>'+text+'</span></li>');
			// TREE.setTreeNodes(temp_node, false);
			// Along with the cleanup actions few lines below
			var temp_node = $("<span>").html('<li class="doc" id="'+id+'"><span>'+text+'</span></li>');
			TREE.setTreeNodes(temp_node, false); // adds a <li line> and a <li lastline> around our <li doc>
			temp_node = temp_node.children(); // three <li> elements
			$("ul", this).first().append(temp_node);
			dragNode_destination = (TREE.getSelected().attr("id") ? TREE.getSelected() : ROOT); //TREE.getSelected();
			dragNode_source = $(temp_node); //.first().next(); // Get our <li doc>
			TREE.moveNodeToFolder(dragNode_destination);
			// Clean up, since somehow, the resulting DOM gets structured like this "line-last" - "doc-last" - "line-last" - "line-last"
			$(dragNode_source.get(0)).attr("class",'line');
			$(dragNode_source.get(2)).remove();
			//temp_node.remove();
			//dragNode_destination.append(dragNode_source);
			if(typeof(callback) == 'function')
			{
				callback(dragNode_destination, dragNode_source);
			}
		};
		TREE.delNode = function(callback)
		{
			if (!dragNode_source.get(0)) {
			    dragNode_source = TREE.getSelected();
			}
			TREE.checkNodeIsLast(dragNode_source[0]);
			//Added: check if parent gets empty
			var parent = $(dragNode_source).parents('li:first');
			var line = $(dragNode_source).prev('.line');
			var nodeSize = $('>ul >li', parent).not('.line, .line-last').filter(':visible').size();
			if(TREE.option.docToFolderConvert && nodeSize==0)
			{
				TREE.removeNodeContainer(parent);
			}else if(nodeSize==0)
			{
				parent[0].className=parent[0].className.replace('open','close');
				$('>ul',parent).hide();
			}
			//end Added
			dragNode_source.prev().remove();
			dragNode_source.remove();
			if(typeof(callback) == 'function')
			{
				callback(dragNode_destination);
			}
		};

		TREE.init = function(obj)
		{
			TREE.setTreeNodes(obj, false);
		};
		TREE.init(ROOT);
	});
}
