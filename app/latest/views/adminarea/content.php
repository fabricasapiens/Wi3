<?php
    //we need the UI and fancybox plugin
    Wi3::inst()->plugins->load("plugin_jquery_ui"); // For dragging and dropping
    Wi3::inst()->plugins->load("plugin_jquery_fancybox"); // For the nice modal box
    
    $site = Wi3::inst()->sitearea->site;
    $page = Wi3::inst()->sitearea->page;
    
    // We need to escape from the 'column' and go full wide, thus the following DOM structure
    
?>
        </div>
    </div>
    <div id='adminarea_content_iframedivs'>
         <div id='adminarea_content_divisionbar'></div>
        <iframe id='wi3_edit_iframe' src='<?php echo Wi3::inst()->urlof->action("adminarea", "content_edit") . "_" . $page->id; ?>' ></iframe>
    </div>
    <div>
        <div>
