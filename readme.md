Wi3
========

#### HTML5 CMS ####

![Wi3 Logo](https://github.com/fabricasapiens/Wi3/raw/v0.8/docs/wi3_logo_blue%20on%20white_100x70.png)

Wi3 is a modern and flexible HTML5 CMS that focuses on a great experience for end-users, designers and developeres alike.

More specifically, these are the intended use-cases

- **End-users** have an extremely visual and simple experience while editing their site. They can browse around the site as usual and can click on any editable part to alter existing contents or to add new.
- **Designers** have the freedom to simply use HTML templates and any CSS or Javascript to style their pages. Beautiful templates are planned to be sold in an in-cms template-store.
- **Developers** can easily write 'components' like a webshop, gallery or maybe fancy 3D rotating page title that can easily be dropped onto a page. Well-working components are planned to be sold in an in-cms component-store.

### License ###
The Wi3 CMS is free and open source, and is meant to be like that for all future. 

In order to support the community with funded dedicated development, we employ a dual-license approach for Wi3.

#### Non-commercial license ####
The Wi3 CMS is licensed under the [Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License](http://creativecommons.org/licenses/by-nc-sa/3.0/). Most importantly, this implies that anyone is free to share and remix the Wi3 CMS, but that the CMS cannot be used for commercial purposes. 

#### Commercial license ####
For commercial use of the Wi3 CMS, please contact us at wi3@fabricasapiens.nl and indicate how you are planning to use Wi3. We will make you a very competitive offer :-)

### Contributions ###
Contributions are more than welcome! To guarantee compliance with the dual-license approach, you are required to explicitly state that the code you provide falls under the [Creative Commons
Attribution 3.0 Unported License](http://creativecommons.org/licenses/by/3.0/). Active contributors that sign a contributors-agreement can gain direct Git access to the code and can receive commercial licences for free.

Installation
========

#### Disclaimer ####
Please note that Fabrica Sapiens cannot be held responsible for anything that results from the following instructions. Moreover, while this setup should also work on Windows and Mac, it is not tested thoroughly on those platforms!

### Downloading and unpacking ###
Basically, there are two options to get Wi3: via a Git client, or via the Github web-interface.

#### Git webinterface ####
Click the 'Download' button above and choose your preferred format. Subsequently, open the downloaded package and extract it to a location of choice. Rename the 'fabricasapiens-Wi3-somename' folder to 'wi3'.

#### Git client ####
Open a terminal and traverse to a directory of choice, in which you want the 'wi3' folder to be created. Subsequently, type
<code>
git clone git://github.com/fabricasapiens/Wi3.git
</code>
Now rename the 'fabricasapiens-Wi3-somename' folder to 'wi3'. 

### Setup ###
Point your browser to the Wi3 folder and append "/setup/" to the end of the URL. An example URL could be <code>http://localhost/wi3/setup/</code>. The installation will walk you through a few steps, after which all important settings are done.

On the last step, click on the link that will take you to the superadmin area.

### Creating a site ###
Creating a site is very easy. Fill in a (unique!) name and title, along with the database-details below, and click 'Create'. 

For now, create a site named and title 'demosite', with 'active' to true. Once you click 'Create', the site will appear at the bottom of the page.

Finally, we couple a URL with the demosite. Backup your www-root/.htaccess, because Wi3 will try to overwrite it! 

For demo purposes, pick the domainname of the URL that is currently displayed in the browser bar (when testing locally for example 'http://127.0.0.1' or 'http://localhost'). Click 'Add new URL' to couple the URL with the demosite. Now click the 'adminarea' link to do some actual content managing!

### Editing Content ###
You can login with the default combination of admin/admin. This is the actual Wi3 CMS that users will see... The first tab is for managing pages, the second tab for managing content and the third tab for managing files. Go ahead and create a page. Now click the 'content' tab and see the magic of an HTML5 CMS! :-)

