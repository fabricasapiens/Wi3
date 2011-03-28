wi3.makeExist("wi3.pagefillers.default");

wi3.pagefillers.default.reloadpageoptionshtml = function() 
{
    var templatename = $("#pagefiller_default_templatename").val();
    var dropzonepreset = $("#pagefiller_default_dropzonepreset").val();
    wi3.request("pagefiller_default_ajax/reloadpageoptionshtml", { "template":templatename, "dropzonepreset": dropzonepreset} );
}

wi3.pagefillers.default.reloadpageoptionstemplatehtml = function() 
{
    var templatename = $("#pagefiller_default_editpage_templatename").val();
    wi3.request("pagefiller_default_ajax/reloadpageoptionstemplatehtml", { "template":templatename } );
}
