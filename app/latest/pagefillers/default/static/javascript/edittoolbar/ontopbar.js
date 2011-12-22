wi3.makeExist("wi3.pagefillers.default");

wi3.pagefillers.default.edittoolbar = {
    
    showPopup : function()
    {
        $("div[type=popup]").slideDown("fast");
    },

    hidePopup : function()
    {
        $("div[type=popup]").hide();
    },
    
    showFormatblockPanel : function()
    {
        // Show the panel from which fields can be inserted
        $("#pagefiller_default_edittoolbar_formatblockpanel").show();
    },
    
    hideFormatblockPanel : function()
    {
        // Show the panel from which fields can be inserted
        $("#pagefiller_default_edittoolbar_formatblockpanel").hide();
    },
    
    showInsertPanel : function()
    {
        // Show the panel from which fields can be inserted
        $("#pagefiller_default_edittoolbar_insertpanel").show();
    },
    
    hideInsertPanel : function()
    {
        // Show the panel from which fields can be inserted
        $("#pagefiller_default_edittoolbar_insertpanel").hide();
    }
};