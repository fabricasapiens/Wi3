//-------------------------------------------------------------
// Executes when full page is loaded (and as such, images etc are loaded as well)
//-------------------------------------------------------------
jQuery( function($) {
	if (wi3.routing.action == "menu") 
	{
		//in "menu" 
	    adminarea.menu_pages_tree();	 			//for moving the pages around in the menu_pages page 
	} 
	else if (wi3.routing.action == "content") 
	{	
	    // in "content", set the correct size of the Iframe, and make sure this stays that way during resizes of the window
        $("#wi3_edit_iframe").css("height", ($(window).height() - $("#wi3_edit_iframe").offset().top -3) + "px");
        $(window).bind("resize",function() {
            $("#wi3_edit_iframe").css("height", ($(window).height() - $("#wi3_edit_iframe").offset().top -3) + "px");
        });
    }
	else if (wi3.routing.action == "files") 
	{
	    //in "files"
	    adminarea.files_files_tree();              //for moving files around in the files menu
	} 
	else if (wi3.routing.action == "users") 
	{
	    //in "users"
	    adminarea.users_users_tree();              //for creating a nice user-list in the users menu
	}
	//make the ajax request indicator work
	$("#wi3_ajax_menu #wi3_ajax_indicator").bind("ajaxSend", function(){	 	
        var amount = ($(this).html()*1)+1;
        $(this).html(amount);
    }).bind("ajaxComplete", function(){
        var amount = ($(this).html()*1)-1;
        $(this).html(amount);
    }).bind("ajaxStart", function(){
        $(this).addClass("working");
    }).bind("ajaxStop", function(){
        $(this).removeClass("working");
    });

});


var adminarea = {

    timeoutvar : null,

    alert : function(html) {
        //make use of the nice notification div on top
        //
        //clear the timeOut that hides the notification after a while
	    clearTimeout(adminarea.timeoutvar);
		//copy top to bottom
		$("#wi3_notification_bottom").html($("#wi3_notification_top").html());
		//if top is visible, we need to show bottom and hide top
		if ($("#wi3_notification_top").is(":visible")) {
		    //hide top, and show bottom
		    $("#wi3_notification_bottom").show();
		    $("#wi3_notification_top").hide();
		}
		//copy new data to top, and show
		$("#wi3_notification_top").html(html).slideDown("fast",function() {$("#wi3_notification_bottom").hide()} );
		//set Timeout to hide the notification
		adminarea.timeoutvar = setTimeout('$("#wi3_notification_top").slideUp()', 3000);
    },
	
	toggleAddPagePanel : function() {
		$('#addPagePositionPanel').slideToggle();
		// Hide any active panel about the currently selected page
		$("#menu_pagesettings_tabs").fadeOut("fast");
	},

	hideAddPagePanel : function() {
		$('#addPagePositionPanel').slideUp();
		// Hide any active panel about the currently selected page
		$("#menu_pagesettings_tabs").fadeOut("fast");
	},
    
    addpageposition : function() {
        var selected = this.currentTree().getSelected();
        if (selected.size() > 0) 
        {
            var id = $(selected.get(0)).attr("id");
            var options =  $("#wi3_adminarea_menu_addpageform").serializeArray();
            options.push({"name":"under", "value":id});
            wi3.request("adminarea_menu_ajax/addpageposition", options );
        }
        else
        {
            wi3.request("adminarea_menu_ajax/addpageposition", $("#wi3_adminarea_menu_addpageform").serializeArray() );
        }
    },
    
    addfolder : function() {
        var selected = this.currentTree().getSelected();
        if (selected.size() > 0) 
        {
            var id = $(selected.get(0)).attr("id");
            var options = {"location":"under", "refid":id};
            wi3.request("adminarea_files_ajax/addfolder", options);
        }
        else
        {
            wi3.request("adminarea_files_ajax/addfolder", {} );
        }
    },

    reload_iframe : function () {
        var iframe = $("#wi3_edit_iframe");
	    var temp = iframe.get(0).src;
	    iframe.get(0).src = "";
	    iframe.get(0).src = temp;
    },

    reload_page : function() {
	    var temp = document.location.href;
	    document.location.href = "";
	    document.location.href = temp;
    },
    
    simpleTreeCollection : {},
    currentTree : function() {
        return adminarea.simpleTreeCollection.get(0);
    },

    menu_pages_tree : function() {
    
		// TODO ? $('#menu_pages').prepend($("<div>", { class:"activeUnderHover" }));
	
	    //enable drag/drop within the tree and display the tree in a nice manner
	    adminarea.simpleTreeCollection = $('#menu_pages').simpleTree({
		    autoclose: false,
		    whileHover:function(node){
		        
		    },
		    afterHover:function(node){
		        // todo
		    },
		    afterClick:function(node){
			    wi3.request("adminarea_menu_ajax/startEditPagepositionSettings/", {pagepositionname: node.attr("id")} );
			    // TODO ? $(".activeUnderHover", adminarea.currentTree()).fadeIn().css("top", node.position().top);
		    },
		    afterDblClick:function(node){
			    //alert("text-"+$('span:first',node).text());
		    },
		    afterMove:function(destination, source, pos){
			    if ($(destination).is("li") && $(destination).attr("id")) {
				    wi3.request("adminarea_menu_ajax/movePagepositionUnder/", {source: source.attr("id"), destination: destination.attr("id")} );
			    } else if ($(destination).next("li").attr("id")) {
				    wi3.request("adminarea_menu_ajax/movePagepositionBefore/", {source: source.attr("id"), destination: destination.next("li").attr("id")} );
			    } else if ($(destination).prev("li").prev("li").prev("li").attr("id")) {  //two more prev() because after the drag, the source itself is placed before destination and should be skipped in this traversal
				    wi3.request("adminarea_menu_ajax/movePagepositionAfter/", {source: source.attr("id"), destination: destination.prev("li").prev("li").prev("li").attr("id")} );
			    } //en anders jammer
		    },
		    whileDrag:function(li, dest) {
		    
		    },
		    afterDrag:function(li, dest) {
		        // afterDrag will *only* be called if there was no move within the tree.
		        // If dropped somewhere within the prullenbak (bin), then delete the page
		        if (dest.closest("#wi3_prullenbak").attr("id")) {
		            wi3.request("adminarea_menu_ajax/deletePageposition/", {pagename: li.attr("id")} );
		            adminarea.currentTree().delNode();
		        }
		    },
		    afterAjax:function()
		    {
			    //alert('Loaded');
		    },
		    animate:true,
		    docToFolderConvert:true
	    });

	    $('#menu_pages a').each(function() {
		    $(this).attr("old_href", $(this).attr("href"));
	    });
	    $('#menu_pages a').attr("href", "javascript:void(0)");
    },
    
    menu_pagesettings_enable : function() {
        // Enable the tabs
        $("#menu_pagesettings_tabs").tabs('destroy'); //'reset'
        $("#menu_pagesettings_tabs").tabs();
        // Make the redirect-switcher work 
        $("#redirect_type").bind("change", function(event) {
            if ($(this).val() == "wi3")
            {
                $("#redirect_external").hide();
                $("#redirect_wi3").fadeIn();
            }
            else if ($(this).val() == "external")
            {
                $("#redirect_wi3").hide();
                $("#redirect_external").fadeIn();
            }
            else
            {
                $("#redirect_wi3").add("#redirect_external").hide();
            }
            
        });
    },

    files_files_tree : function() {
	    
	    //enable drag/drop within the tree and display the tree in a nice manner
	    adminarea.simpleTreeCollection = $('#files_files').simpleTree({
		    autoclose: false,
		    whileHover:function(node){
		        
		    },
		    afterHover:function(node){
		        // todo
		    },
		    afterClick:function(node){
			    wi3.request("adminarea_files_ajax/startEditFileSettings/", {fileid: node.attr("id")} );
			    // TODO ? $(".activeUnderHover", adminarea.currentTree()).fadeIn().css("top", node.position().top);
		    },
		    afterDblClick:function(node){
			    //alert("text-"+$('span:first',node).text());
		    },
		    afterMove:function(destination, source, pos){
		        //  TODO: only drop on folder
			    if ($(destination).is("li") && $(destination).attr("id")) {
				    wi3.request("adminarea_files_ajax/moveFileUnder/", {source: source.attr("id"), destination: destination.attr("id")} );
			    } else if ($(destination).next("li").attr("id")) {
				    wi3.request("adminarea_files_ajax/moveFileBefore/", {source: source.attr("id"), destination: destination.next("li").attr("id")} );
			    } else if ($(destination).prev("li").prev("li").prev("li").attr("id")) {  //two more prev() because after the drag, the source itself is placed before destination and should be skipped in this traversal
				    wi3.request("adminarea_files_ajax/moveFileAfter/", {source: source.attr("id"), destination: destination.prev("li").prev("li").prev("li").attr("id")} );
			    } //en anders jammer
		    },
		    whileDrag:function(li, dest) {
		    
		    },
		    afterDrag:function(li, dest) {
		        // afterDrag will *only* be called if there was no move within the tree.
		        // If dropped somewhere within the prullenbak (bin), then delete the page
		        if (dest.closest("#wi3_prullenbak").attr("id")) {
		            wi3.request("adminarea_files_ajax/deleteFile/", {filename: li.attr("id")} );
		            adminarea.currentTree().delNode();
		        }
		    },
		    afterAjax:function()
		    {
			    //alert('Loaded');
		    },
		    animate:true,
		    docToFolderConvert: false
	    });

	    $('#menu_files a').each(function() {
		    $(this).attr("old_href", $(this).attr("href"));
	    });
	    $('#menu_files a').attr("href", "javascript:void(0)");
    },
    
    files_filesettings_enable : function() {
        //enable the tabs
        $("#files_filesettings_tabs").tabs('destroy'); //'reset'
        $("#files_filesettings_tabs").tabs();
    },    
    
    users_users_tree : function() {
        adminarea.simpleTreeCollection = $('#users_users').simpleTree({
		    autoclose: false,
		    animate:true,
		    docToFolderConvert:false,
		    afterClick:function(node){
			    wi3.request("adminarea_menu_ajax/startEditUserSettings/" + node.attr("id"));
		    }
		});
    },
    
    users_usersettings_enable : function() {
        //enable the tabs
        $("#users_usersettings_tabs").tabs('destroy'); //'reset'
        $("#users_usersettings_tabs").tabs();
    }
    
    
    /*,

    wi3_edit_page_settings : function(elm) {
	    wi3.request("engine/startEditPageSettings/" + $(elm).parent().parent().attr("id"), {});
    }*/
}
