RewriteEngine On

# Apache's own requests can crash caches. See http://inventivelabs.com.au/weblog/post/apache-s-internal-dummy-connection
RewriteCond %{HTTP_USER_AGENT} ^.*internal\ dummy\ connection.*$ [NC]
RewriteRule .* - [F,L]

# If we have redirected and get here again, there's something wrong
RewriteCond %{ENV:REDIRECT_REDIRECTED} ^TRUE$
RewriteRule .* - [F,L]

# Reroute to correct vhost
RewriteCond %{SERVER_NAME} ^<?php echo $domain; ?>$ [NC]
RewriteRule .* vhosts/<?php echo $domain; ?>/httpdocs/$0/ [E=REDIRECTED:TRUE,L]
