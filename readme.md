Wi3
========

#### HTML5 CMS ####

Wi3 is a modern and flexible HTML5 CMS that focuses on a great experience for end-users, designers and developeres alike.

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
Please note that Fabrica Sapiens cannot be held responsible for anything that results from the following instructions. Moreover, Windows and Mac systems are not officially supported.

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

### File permissions ###
For Wi3 to work,the app needs write access to four folders. 

- app/latest/logs
- app/latest/cache
- sites
- vhosts

### Routing to the cms ###
In order to access wi3, you will need to make it accessible through a URL. Usually, this involves a .htaccess file that points to the Wi3 installation.

You could do this by placing something along these lines in a .htaccess file in your www-root

    RewriteEngine On 
    
    RewriteCond %{SERVER_NAME} ^127.0.0.1$ [NC]
    RewriteRule (.*) /wi3/vhosts/%{HTTP_HOST}/httpdocs/$1/ [E=REDIRECTED:TRUE,L]

The specific code above will only allow connections from http://127.0.0.1 and point it to the 127.0.0.1 vhost folder. This is desired for local development. On a deployed server, you want to change 127.0.0.1 with your actual hostname.

Note that the above .htaccess will not allow connections from http://localhost! If you want http://localhost to work along with http://127.0.0.1, you should make a vhost folder named 'localhost' and change the .htaccess into something like this

    RewriteEngine On 
    RewriteRule (.*) /wi3/vhosts/%{HTTP_HOST}/httpdocs/$1/ [E=REDIRECTED:TRUE,L]

This guide however assumes access from http://127.0.0.1.

As can be seen in the .htaccess file, all requests from 127.0.0.1 are routed to the <code>/wi3/vhosts/127.0.0.1/</code> folder. This folder should exist, and should contain at least a 'log' and a 'httpdocs' subfolder. The 'httpdocs' folder should subsequently contain a .htaccess file that does the final routing to the proper places. See the file as included in the download for details.

### DB setup ###
Congratulations, you have Wi3 working. Now we only need to get the Database up and running so the cms can do some actual work!

First of all, rename <code>app/config/database.php.example</code> into <code>app/config/database.php</code> and edit the file to match your database setup. It is strongly advised to give Wi3 a dedicated database, and not a shared one!

Now, go to <code>http://127.0.0.1/_wi3controllers/setup/</code>. You will be presented with a bare interface to setup the first necessary tables. Simply click the links from top to bottom.

Once you are done, you have enabled the 'superadmin' user and you can start managing sites!

### Creating a site ###
PLEASE NOTE: The CMS is currently Dutch. There will be an english version later; in the meantime, you might be able to guess quite some words by mixing German with English...

Point your browser to <code>http://127.0.0.1/superadminarea</code> and login with superadmin/superadmin. As you can see, there are no sites yet. Fill the top input with 'demosite' (this is the site that the .htaccess in the 127.0.0.1 points to) and give it a title (e.g. 'local demosite') in the second input. Then set the select to 'ja' and click 'aanmaken'. The CMS will now create a site along with a default admin  login.

Congratulations, you now have everything working to log into the CMS and do some actual content managing!

### Editing Content ###
Go to <code>http://127.0.0.1/adminarea/</code> and login with admin/admin. This is the actual Wi3 CMS that users will see... The first tab is for managing pages, the second tab for managing content and the third tab for managing files. Go ahead and create a page. Now do an F5 refresh to show the page (this refresh is only required with the creation of the first page). Now click the 'inhoud' tab and see the magic of an HTML5 CMS! :-)

[ TODO: expand readme ]

