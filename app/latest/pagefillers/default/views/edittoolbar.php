<div id='pagefiller_default_edittoolbar'">
    <div id='pagefiller_default_edittoolbar_content' style='position: relative; left: 50%; margin-left: -480px; width: 960px;'>
        <div style='position: relative; z-index: 10; height: 35px; overflow: hidden; padding: 5px; margin-left: 10px; '>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-text-bold.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().boldSelection();'></img>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-text-italic.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().italicSelection();'></img>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-text-underline.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().underlineSelection();'></img>
            <div style='display: inline; visibility: hidden; padding-left: 10px;'></div>
            <?php
            /*
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-indent-less.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().outdentSelection();'></img>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-indent-more.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().indentSelection();'></img>
            <div style='display: inline; visibility: hidden; padding-left: 10px;'></div>
            */
            ?>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-list-ol.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().toggleOrderedList();'></img>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-list-ul.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().toggleUnorderedList();'></img>
            <div style='display: inline; visibility: hidden; padding-left: 10px;'></div>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-justify-left.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().alignSelection("left");'></img>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-justify-center.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().alignSelection("center");'></img>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-justify-right.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().alignSelection("right");'></img>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "format-justify-fill.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.getActiveEditor().alignSelection("full");'></img>
            <div style='display: inline; visibility: hidden; padding-left: 10px;'></div>
            <span style='color:#444; padding-bottom: 25px;' onMouseOver='wi3.pagefillers.default.edittoolbar.showFormatblockPanel();' onMouseOut='wi3.pagefillers.default.edittoolbar.hideFormatblockPanel();'>bloktype...</span>
            <div style='display: inline; visibility: hidden; padding-left: 10px;'></div>
            <span style='color:#444; padding-bottom: 25px;' onMouseOver='wi3.pagefillers.default.edittoolbar.showInsertPanel();' onMouseOut='wi3.pagefillers.default.edittoolbar.hideInsertPanel();'>invoegen...</span>
            <div style='display: inline; visibility: hidden; padding-left: 10px;'></div>
            <img style='vertical-align: middle;' src='<?php echo $iconurl . "document-save.png"; ?>' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.saveAllEditableBlocks();'></img>
            <div style='display: inline; visibility: hidden; padding-left: 3px;'></div>
            <span style='color:#444;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.saveAllEditableBlocks();'><button>opslaan</button></span>
        </div>
        <div id='pagefiller_default_edittoolbar_formatblockpanel' onMouseOver='wi3.pagefillers.default.edittoolbar.showFormatblockPanel();' onMouseOut='wi3.pagefillers.default.edittoolbar.hideFormatblockPanel();' style='display: none; position: absolute; z-index: 10; left: 200px; height: 30px; width: 200px; height: 100px; padding: 10px; overflow: auto; background: #fff; border: 1px solid #ddd; border-top: none;'>
            <button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.formatblock("<p>");'>Paragraaf</button>
            <button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.formatblock("<h1>");'>Kop 1</button>
            <button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.formatblock("<h2>");'>Kop 2</button>
            <button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.formatblock("<h3>");'>Kop 3</button>
            <button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.formatblock("<quote>");'>Quote</button>
        </div>
        <div id='pagefiller_default_edittoolbar_insertpanel' onMouseOver='wi3.pagefillers.default.edittoolbar.showInsertPanel();' onMouseOut='wi3.pagefillers.default.edittoolbar.hideInsertPanel();' style='display: none; position: absolute; z-index: 10; left: 300px; height: 30px; width: 200px; height: 100px; padding: 10px; overflow: auto; background: #fff; border: 1px solid #ddd; border-top: none;'>
            <button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.insertField("image");'>afbeelding</button>
			<button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.insertField("simpleblogoverview");'>Overzicht van blogartikelen</button>
			<button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.insertField("simpleblogarticle");'>Blogartikel</button>
            <button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.insertField("link");'>link</button>
            <button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.insertField("imageshop");'>fotowinkel</button>
            <?php // <button style='float: left; cursor:pointer;' onClick='$("#wi3_edit_iframe").get(0).contentWindow.wi3.pagefillers.default.edittoolbar.insertField("simpleblog");'>weblog</button> ?>
        </div>
    </div>
</div> 
