RewriteEngine On

# Direct Site and Wi3 locations are prepended with a _ (e.g. _static or _wi3controller)
# Only exceptions are the locations that appear in "visible" URLS: adminarea and superadminarea
# Pagenames are always without a prepending _ (e.g. Home or News)

###
# Now **FIRST** set the sitename; this is used within PHP to determine which site should be loaded
###
SetEnv SITENAME <?php echo $sitename; ?>

#### 
# Site locations
####

# Direct access to the site files
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule _sitefiles/(.*) <?php echo $wi3path; ?>sites/<?php echo $sitename; ?>/$1  [L]

# Static site content
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule _static/(.*) <?php echo $wi3path; ?>sites/<?php echo $sitename; ?>/data/static/$1  [L]

# Uploads
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule _uploads/(.*) <?php echo $wi3path; ?>sites/<?php echo $sitename; ?>/data/uploads/$1 [L]

#### 
# Special Wi3 controllers adminarea and superadminarea
####

# Superadminarea (should it be available on every site?)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_URI} ^/<?php echo $currentpath; ?>/superadminarea
RewriteRule superadminarea(.*)$ <?php echo $wi3path; ?>app/index.php/superadminarea$1/ [L]

# Admin area
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_URI} ^/<?php echo $currentpath; ?>/adminarea
RewriteRule adminarea(.*)$ <?php echo $wi3path; ?>app/index.php/adminarea$1/ [L]

#### 
# Wi3 locations
####

# _Wi3files content.
# The redirect to 'latest' will resolve to the latest app version (e.g. 0.7.2 or 1.0 or whatever version is newest)
# remove a trailing slash, if there is any
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} \/$
RewriteRule _wi3files/(.*)[.]{1}$ <?php echo $wi3path; ?>app/latest/$1  [L]
# Otherwise, if there was no trailing slash, just redirect
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule _wi3files/(.*)$ <?php echo $wi3path; ?>app/latest/$1  [L]

# Any Wi3 controller (not files!)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_URI} _wi3controllers
RewriteRule _wi3controllers/(.*)$ <?php echo $wi3path; ?>app/index.php/$1/ [L]

####
# Finally, the 'normal' site pages
####

# Any page
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule (.*) <?php echo $wi3path; ?>app/index.php/sitearea/view/$1 [L]
