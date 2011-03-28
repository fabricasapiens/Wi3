<?php

    $page = Wi3::inst()->sitearea->page;
    $site = Wi3::inst()->sitearea->site;

   // First, load the CSS
   $this->css("reset.css");
   $this->css("style.css");
   
   // Load JQuery UI (also loads Jquery Core via dependencies)
   Wi3::inst()->plugins->load("plugin_jquery_ui");
   
   // Load some extra JS for Cufon
   $this->javascript("cufon-yui.js");
   $this->javascript("arial.js");
   $this->javascript("cuf_run.js");
   $this->javascript("radius.js");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
Design by http://www.rocketwebsitetemplates.com
Released for free under a Creative Commons Attribution 3.0 License
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RocketWebsiteTemplates.com</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="main">

  <div class="header">
    <div class="header_resize">
      <div class="menu_nav">
        <ul>
          <li class="active"><a href="index.html">Home</a></li>
          <li><a href="support.html">Support</a></li>
          <li><a href="about.html">About Us</a></li>
          <li><a href="blog.html">Blog</a></li>
          <li><a href="contact.html">Contact Us</a></li>
        </ul>
      </div>
      <div class="clr"></div>
      <div class="logo"><h1><a href="index.html">Free<span>sim</span> <small>put your slogan here</small></a></h1></div>
      <div class="clr"></div>
    </div>
  </div>

  <div class="content">
    <div class="content_resize">
      <div class="mainbar">
        <div class="article">
          <h2><span>Template</span> License</h2><div class="clr"></div>
          <p><span class="date">Date: <a href="#">16.03.2018</a></span> &nbsp;|&nbsp; Posted by <a href="#">Owner</a> &nbsp;|&nbsp; Filed under <a href="#">templates</a>, <a href="#">internet</a></p>
          <a href="#" class="com">11</a>
          <img src="images/img1.jpg" width="625" height="205" alt="image" />
          <p>This is a free CSS website template by <a href="http://www.rocketwebsitetemplates.com/" title="Website Templates">RocketWebsiteTemplates.com</a>. This 
work is distributed under the <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 License</a>, 
which means that you are free to use it for any personal or commercial purpose provided you credit me in the form of a link back to <a href="http://www.rocketwebsitetemplates.com/" title="Website Templates">RocketWebsiteTemplates.com</a>.</p>
          <p class="spec"><a href="#" class="rm">Read more &raquo;</a></p>
        </div>
        <div class="article">
          <h2><span>Office</span> interior</h2><div class="clr"></div>
          <p><span class="date">Date: <a href="#">15.03.2010</a></span> &nbsp;|&nbsp; Posted by <a href="#">Owner</a> &nbsp;|&nbsp; Filed under <a href="#">templates</a>, <a href="#">internet</a></p>
          <a href="#" class="com">7</a>
          <img src="images/img2.jpg" width="625" height="205" alt="image" />
          <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec libero. Suspendisse bibendum. Cras id urna. <a href="#">Morbi tincidunt, orci ac convallis aliquam, lectus turpis varius lorem, eu posuere nunc justo tempus leo.</a> Donec mattis, purus nec placerat bibendum, dui pede condimentum odio, ac blandit ante orci ut diam. Cras fringilla magna. Phasellus suscipit, leo a pharetra condimentum, lorem tellus eleifend magna, eget fringilla velit magna id neque. Curabitur vel urna. In tristique orci porttitor ipsum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec libero. Suspendisse bibendum. Cras id urna. Morbi tincidunt, orci ac convallis aliquam.</p>
          <p class="spec"><a href="#" class="rm">Read more &raquo;</a></p>
        </div>
        <p class="pages"><small>Page 1 of 2 &nbsp;&nbsp;&nbsp;</small> <span>1</span> <a href="#">2</a> <a href="#">&raquo;</a></p>
      </div>
      <div class="sidebar">
        <div class="searchform">
          <form id="formsearch" name="formsearch" method="post" action="">
            <span><input name="editbox_search" class="editbox_search" id="editbox_search" maxlength="80" value="Search our ste:" type="text" /></span>
            <input name="button_search" src="images/search_btn.gif" class="button_search" type="image" />
          </form>
        </div>
        <div class="gadget">
          <h2 class="star"><span>Sidebar</span> Menu</h2><div class="clr"></div>
          <ul class="sb_menu">
            <li><a href="#">Home</a></li>
            <li><a href="#">TemplateInfo</a></li>
            <li><a href="#">Style Demo</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Archives</a></li>
            <li><a href="http://www.dreamtemplate.com/">Web Templates</a></li>
          </ul>
        </div>
        <div class="gadget">
          <h2 class="star"><span>Sponsors</span></h2><div class="clr"></div>
          <ul class="ex_menu">
            <li><a href="http://www.dreamtemplate.com" title="Website Templates">DreamTemplate</a><br />Over 6,000+ Premium Web Templates</li>
            <li><a href="http://www.templatesold.com/">TemplateSOLD</a><br />Premium WordPress &amp; Joomla Themes</li>
            <li><a href="http://www.imhosted.com" title="Affordable Hosting">ImHosted.com</a><br />Affordable Web Hosting Provider</li>
            <li><a href="http://www.myvectorstore.com" title="Stock Icons">MyVectorStore</a><br />Royalty Free Stock Icons</li>
            <li><a href="http://www.evrsoft.com" title="Website Builder">Evrsoft</a><br />Website Builder Software &amp; Tools</li>
            <li><a href="http://www.csshub.com/" title="CSS Templates">CSS Hub</a><br />Premium CSS Templates</li>
          </ul>
        </div>
      </div>
      <div class="clr"></div>
    </div>
  </div>

  <div class="fbg">
    <div class="fbg_resize">
      <div class="col c1">
        <h2><span>Image Gallery</span></h2>
        <a href="#"><img src="images/pix1.jpg" width="58" height="58" alt="pix" /></a>
        <a href="#"><img src="images/pix2.jpg" width="58" height="58" alt="pix" /></a>
        <a href="#"><img src="images/pix3.jpg" width="58" height="58" alt="pix" /></a>
        <a href="#"><img src="images/pix4.jpg" width="58" height="58" alt="pix" /></a>
        <a href="#"><img src="images/pix5.jpg" width="58" height="58" alt="pix" /></a>
        <a href="#"><img src="images/pix6.jpg" width="58" height="58" alt="pix" /></a>
      </div>
      <div class="col c2">
        <h2><span>Lorem Ipsum</span></h2>
        <p>Lorem ipsum dolor<br />Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec libero. Suspendisse bibendum. Cras id urna. <a href="#">Morbi tincidunt, orci ac convallis aliquam</a>, lectus turpis varius lorem, eu posuere nunc justo tempus leo. Donec mattis, purus nec placerat bibendum, dui pede condimentum odio, ac blandit ante orci ut diam.</p>
      </div>
      <div class="col c3">
        <h2><span>Contact</span></h2>
        <p>Praesent dapibus, neque id cursus faucibus, tortor neque egestas augue.</p>
        <p><a href="mailto:support@yoursite.com">support@yoursite.com</a></p>
        <p>+1 (123) 444-5677<br />+1 (123) 444-5678</p>
        <p>Address: 123 TemplateAccess Rd1</p>
      </div>
      <div class="clr"></div>
    </div>
  </div>
  <div class="footer">
    <div class="footer_resize">
      <p class="lf">Layout by Rocket <a href="http://www.rocketwebsitetemplates.com/">Website Templates</a></p>
      <ul class="fmenu">
        <li class="active"><a href="index.html">Home</a></li>
        <li><a href="support.html">Support</a></li>
        <li><a href="blog.html">Blog</a></li>
        <li><a href="about.html">About Us</a></li>
        <li><a href="contact.html">Contacts</a></li>
      </ul>
      <div class="clr"></div>
    </div>
  </div>
</div>
</body>
</html>
