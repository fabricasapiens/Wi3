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
PLEASE NOTE: While the setup is both Dutch and English, the CMS is currently only in Dutch. There will be an english version later; in the meantime, you might be able to guess quite some words by mixing German with English...

Creating sites is currently not very easy, and a clean user-interface for this is the first thing on the todo list. There are basically three steps:

1. Ensure that the correct files reside on disk in the <code>sites</code> folder. The <code>demosite</code> site will already be there and serves as an example.
2. Ensure that the database-settings for the site are correct. This is important because Wi3 will create another separate database for every new site you enable. Wi3 will use a site-specific database-config for that, as found in <code>sites/[sitename]/config/database.php</code>. For the <code>demosite</code> site, rename <code>sites/demosite/config/database.php.example</code> into <code>sites/demosite/config/database.php</code> and edit the file so that the database user is valid. Do **not change** the databasename. Note that Wi3 requires a DB account that has the privileges to indeed create the desired database!
3. Create a site through the superadminarea.

As you can see, there are no sites yet. Fill the top input with 'demosite'. The top input will be used to get the correct site in the <code>sites</code> folder. In the second input, give the site a title (e.g. 'local demosite' but anything works). Then set the select to 'ja' and click 'aanmaken'. The CMS will now create a site along with a default admin login.

Congratulations, you now have everything working to log into the CMS and do some actual content managing!

### Editing Content ###
Go to <code>http://127.0.0.1/adminarea/</code> and login with admin/admin. This is the actual Wi3 CMS that users will see... The first tab is for managing pages, the second tab for managing content and the third tab for managing files. Go ahead and create a page. Now do an F5 refresh to show the page (this refresh is only required with the creation of the first page). Now click the 'inhoud' tab and see the magic of an HTML5 CMS! :-)

[ TODO: expand readme ]

