<?php

    /**
     * Set the default time zone.
     * @see  http://php.net/timezones
     */
    date_default_timezone_set('Europe/Amsterdam');

    /**
     * Set the default locale.
     * @see  http://php.net/setlocale
     */
    setlocale(LC_ALL, 'en_US.utf-8');

    session_name("wi3setup");
    session_start();
    
    ini_set('display_errors',1);
    error_reporting(E_ALL|E_STRICT);

    // Check whether language is explicitly set
    if (isset($_POST["lang"]))
    {
        $expire = time()+60*60*24*30; // 1 month
        setcookie("lang", $_POST["lang"], $expire);
        $_COOKIE["lang"] = $_POST["lang"];
        $_SESSION["lang"] = $_POST["lang"];
    }
    
?>

<html>
    <head>
        <link  type="text/css" href="static/css/style.css" rel="stylesheet" media="all" />
    </head>
    <body>
        <div id='wrap'>


<?php

    // Language function
    function __($string)
    {
        global $lang;
        $strings = Array(
            "nl-nl" => Array(
                "language" => "taal",
                "folders" => "mappen",
                "writability" => "schrijfbaarheid",
                "writable" => "schrijfbaar",
                "not writable" => "niet schrijfbaar",
                "security" => "beveiliging",
                "database" => "database"
            )
        );
        if (isset($strings[$lang][$string]))
        {
            return $strings[$lang][$string];
        }
        else 
        {
            return $string;
        }
    }
    
    // Return a nice path without .. and .
    /**
     * This function is to replace PHP's extremely buggy realpath().
     * @param string The original path, can be relative etc.
     * @return string The resolved path, it might not exist.
     */
    function truepath($path)
    {
        $originalpath = $path;
        // attempts to detect if path is relative in which case, add cwd
        if(strpos($path,':')===false && (strlen($path)==0 || $path{0}!='/'))
        {
            $path=getcwd().DIRECTORY_SEPARATOR.$path;
        }
        // resolve path parts (single dot, double dot and double delimiters)
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.'  == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        $path=($originalpath[0]=="/"?"/":"") . implode(DIRECTORY_SEPARATOR, $absolutes);
        // if file exists and it is a link, use readlink to resolves links
        //if(file_exists($path) && linkinfo($path)>0)$path=readlink($path);
        return $path;
    }
    
    function savesetupconfig($configarray)
    {
        $export = "<?php defined('SETUP') or die('No direct script access.'); return " . var_export($configarray, true) . "; ?>";
        @file_put_contents("./.setupconfig.php", $export);
    }
    
    // Determine language
    if (isset($_COOKIE["lang"]))
    {
        $lang = $_COOKIE["lang"];
        if(!in_array($lang, array('nl-nl', 'en-us'))) 
        {
           // check the allowed languages, and force the default
           $lang = 'nl-nl';
        }
    }
    else
    {
        $lang = "nl-nl";
    }
    // Set language in session as well, if not already there 
    if (!isset($_SESSION["lang"]))
    {
        $_SESSION["lang"] = $lang;
    }
    
    // Load setupconfig
    define("SETUP", TRUE);
    if (file_exists(".setupconfig.php"))
    {
        $setupconfig = include(".setupconfig.php");
    }
    if (!isset($setupconfig) OR !is_array($setupconfig)) { $setupconfig = Array("step" => 1); }
    
    // Preliminary determine progress of installation 
    if (isset($_POST["step"]) AND is_numeric($_POST["step"]))
    {
         // TODO: do only allow access to later steps if the request came from a previous step
        $step = $_POST["step"];
    }
    else if (isset($setupconfig["step"]))
    {
        $step = $setupconfig["step"]; // Continue setup from last stop
    }
    else 
    {
        $step = 1; // Start of setup
    }
    
    // Login. Only necessary if the user has already set the required username/password
    if (isset($setupconfig["username"]) AND isset($setupconfig["password"]))
    {
        // Try to login if POST login is supplied
        if (isset($_POST["username"]) AND !empty($_POST["username"]) AND isset($_POST["password"]) AND !empty($_POST["password"]))
        {
            // Check login
            if ($_POST["username"] === $setupconfig["username"] AND md5($_POST["password"]) === $setupconfig["password"])
            {
                // Save security settings to session
                $_SESSION["setup_username"] = $_POST["username"];
                $_SESSION["setup_password"] = md5($_POST["password"]);
                $_SESSION["setup_originalpassword"] = $_POST["password"];
                // Setup will continue from last step (see above)
            }
            else 
            {
                $step = "login"; // Login failed, so go to the login again
            }
        }
        // Force user to login if username/password combi is present in the configfile, and a) they are not present in the session, b) they do not match in the session (TODO)
        if (!isset($_SESSION["setup_username"]) OR !isset($_SESSION["setup_password"]))
        {
            $step = "login";
        }
    }
    
    // Take action at any step 
    if ($step == 1)
    {
        // User chooses language here, and this gets processed at the next step.
    }
    elseif ($step == 2)
    {
        // Processing choosen language, has already been done above due to cookie-setting
        
        // Create status report for the folders
        // Test whether all maps are writable
        $checkpaths = Array(
            dirname(__FILE__ ). "/" => FALSE,
            dirname(__FILE__ ). "/../app/latest/logs/" => FALSE,
            dirname(__FILE__ ). "/../app/latest/config/" => FALSE,
            dirname(__FILE__ ). "/../app/latest/cache/" => FALSE,
            dirname(__FILE__ ). "/../sites/" => FALSE,
        );
        $paths = Array();
        foreach($checkpaths as $path => $writable)
        {
            $path = truepath($path);
            $paths[$path] = is_writable($path);
        }
        // Save step of installation in the setupconfig 
        $setupconfig["step"] = 2;
        savesetupconfig($setupconfig);
        
        // Check whether the user has PHP 5.3 installed. Wi3 needs it to use Late Static Binding
        if (strnatcmp(phpversion(),'5.3') >= 0) 
        { 
            // PHP 5.3 is present. Ok.
        } 
        else 
        { 
            // PHP 5.3 not present. Fail.
            $step = "phpversiontoolow";
        } 
    }
    elseif ($step == 3)
    {
        // Test whether all folders are writable
        $checkpaths = Array(
            dirname(__FILE__ ). "/" => FALSE,
            dirname(__FILE__ ). "/../app/latest/logs/" => FALSE,
            dirname(__FILE__ ). "/../app/latest/config/" => FALSE,
            dirname(__FILE__ ). "/../app/latest/cache/" => FALSE,
            dirname(__FILE__ ). "/../sites/" => FALSE,
        );
        $paths = Array();
        foreach($checkpaths as $path => $writable)
        {
            $path = truepath($path);
            if (!is_writable($path))
            {
                // We should not continue, but simply stay at step 2
                $step = 2;
            }
            $paths[$path] = is_writable($path);
        }
        // Save step of installation in the setupconfig 
        $setupconfig["step"] = 3;
        savesetupconfig($setupconfig);
    }
    elseif ($step == 4)
    {
        // Check whether both username and password are provided
        if (isset($_POST["username"]) AND !empty($_POST["username"]) AND isset($_POST["password"]) AND !empty($_POST["password"]))
        {        
            // Save security settings to session
            $_SESSION["setup_username"] = $_POST["username"];
            $_SESSION["setup_password"] = md5($_POST["password"]);
            $_SESSION["setup_originalpassword"] = $_POST["password"];
            // Save security settings to .setupconfig.php file
            $setupconfig = Array("username" => $_POST["username"], "password" => md5($_POST["password"]), "originalpassword" => $_POST["password"], "step" => 4);
            savesetupconfig($setupconfig);
        }
        // Only allow access to step 4 if credentials are saved in the setupconfig (and thus the user is logged in, when he gets here)
        if (!isset($setupconfig["username"]))
        {
            $step = 3;
        }
    }
    elseif ($step == 5)
    {
        // Only allow access to step 5 if credentials are saved in the setupconfig (and thus the user is logged in, when he gets here)
        if (!isset($setupconfig["username"]))
        {
            $step = 3;
        }
        // Check if the user set the db config from step 4
        if (isset($_POST["dbusername"]) AND isset($_POST["dbpassword"]) AND isset($_POST["dbexistingornew"]) AND isset($_POST["dbname"]))
        {
            // Save config to setupconfig
            $setupconfig["dbusername"] = $_POST["dbusername"];
            $setupconfig["dbpassword"] = $_POST["dbpassword"];
            $setupconfig["dbexistingornew"] = $_POST["dbexistingornew"];
            $setupconfig["dbname"] = $_POST["dbname"];
            // Create or use Database!
            $dbname = $_POST["dbname"];
            $dbokay = TRUE;
            for($i=0;$i<1;$i++) // Just do it one time, but now we can use the break command...
            {
                // Try connection
                @$con = mysql_connect("localhost",$_POST["dbusername"],$_POST["dbpassword"]);
                if (!$con)
                {
                    $dbokay = FALSE;
                    $message = __("Connection to database could not be established. Please try again.");
                    break;
                }
                // Save the grants of the current user 
                $result = mysql_query("SHOW GRANTS FOR CURRENT_USER");
                $grants = Array();
                while($row = mysql_fetch_array($result))
                {
                    $grants[] = $row;
                }
                $hasallprivileges = FALSE;
                foreach($grants as $grant)
                {
                    if (strpos($grant[0], "GRANT ALL PRIVILEGES ON *.* TO ") === 0)
                    {
                        // User has all privileges for all dbs, so that's fine
                        $hasallprivileges = TRUE;
                        break; // break from foreach
                    }
                }

                if ($_POST["dbexistingornew"] == "existing")
                {
                    // Try if existing db exists
                    $db_selected = mysql_select_db($dbname, $con);
                    if ($db_selected == FALSE) {
                        $dbokay = FALSE;
                        $message = __("Database '" . $dbname . "' does not exist. Please try again.");
                        break;
                    }
                    // Now check whether we have the rights to create tables in the db 
                    $hasprivileges = FALSE;
                    if ($hasallprivileges)
                    {
                        $hasprivileges = TRUE;
                    }
                    else 
                    {
                        foreach($grants as $grant)
                        {
                            if (strpos($grant[0], "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, REFERENCES, INDEX, ALTER") === 0 AND (strpos($grant[0], "ON `" . $dbname . "`") > 0 OR strpos($grant[0], "ON `" . addcslashes($dbname, "_") . "`") > 0))
                            {
                                // User has privileges for the $dbname db, so that's fine
                                $hasprivileges = TRUE;
                                break; // break from foreach
                            }
                        }
                    }
                    // Final check for db privileges
                    if ($hasprivileges === FALSE)
                    {
                        $dbokay = FALSE;
                        $message = __("User does not have the proper rights to use database '" . $dbname . "'. Please try again.");
                        break;
                    }
                }
                else 
                {
                    // Check if we can create the new DB
                    if ($hasallprivileges)
                    {
                        if (!mysql_query("CREATE DATABASE " . $dbname,$con))
                        {
                            $dbokay = FALSE;
                            // Check if there was an error because the db already existed
                            $db_selected = mysql_select_db($dbname, $con);
                            if ($db_selected) 
                            {
                                $message = __("User was unable to create database '" . $dbname . "' because it already exists. Please delete the db manually or select the 'existing' option to use the existing database.");
                            }
                            else 
                            {
                                $message = __("User was unable to create database '" . $dbname . "', despite having the rights to do so. Please try again.");
                            }
                            break;
                        }
                    }
                    else 
                    {
                        $dbokay = FALSE;
                        $message = __("User does not have the proper rights to create database '" . $dbname . "'. Please try again.");
                        break;
                    }
                }
            }
            if (!$dbokay)
            {
                $step = 4; // Go back to the fourth step, and let the user enter the db details again
            }
            else 
            {
                // DB settings are okay, so write the Wi3 config file 
                define("SYSPATH", TRUE);
                $wi3databaseconfig = file_get_contents("../app/latest/config/database.php.example");
                // Do some simple replaces
                $wi3databaseconfig = preg_replace("@\'username\'.*@", "'username' => '" . $_POST["dbusername"] . "',", $wi3databaseconfig);
                $wi3databaseconfig = preg_replace("@\'password\'.*@", "'password' => '" . $_POST["dbpassword"] . "',", $wi3databaseconfig);
                $wi3databaseconfig = preg_replace("@\'database\'.*@", "'database' => '" . $_POST["dbname"] . "',", $wi3databaseconfig);
                $wi3databaseconfig = preg_replace("@dbname\=\w*@", "dbname=" . $_POST["dbname"], $wi3databaseconfig);
                file_put_contents("../app/latest/config/database.php", $wi3databaseconfig);
                
                // Fetch the URL of the current wi3 installation
                $url = (strpos($_SERVER["SERVER_PROTOCOL"], "HTTPS")>0?"https":"http") . "://" . $_SERVER["HTTP_HOST"] . truepath($_SERVER["REQUEST_URI"] . "/../");
                // Ensure trailing slash at the end
                if ($url[strlen($url)-1] != "/") { $url .= "/"; }
                
                // Call the Setup controller within Wi3 to actually generate the tables
                // This Setup controller will check the login via the same mechanism as this setup 
                session_write_close(); // Close session to prevent locks
                // Prepare the cookie to send along, so the Kohana setup knows which session to load (thanks @Darsstar!)
                $opts = array(
                    'http'=>array(
                        'method'=>"GET",
                        'header'=>"Cookie: ".session_name()."=".session_id()."\r\n"
                    )
                );
                $context = stream_context_create($opts);
                $result = file_get_contents($url . "app/setup",false, $context);
                if ($result == "tables sucessfully created")
                {
                    // Ok
                    // Set the current step to 5
                    $setupconfig["step"] = 5;
                    unset($setupconfig["originalpassword"]); // Unset the clear and unprotected password
                    unset($_SESSION["originalpassword"]); // Unset the clear and unprotected password
                }
                else 
                {
                    // Didn't work
                    $step = 4;
                    $message = __("Database creation was successful, but tables therein could not be created. Please try again.");
                    $setupconfig["step"] = 4;
                }
            }
        }
        savesetupconfig($setupconfig);
        
        // If the setup continues from step 5 without doing the above processing for step 4, we still need to fetch the URL of the current wi3 installation
        $url = (strpos($_SERVER["SERVER_PROTOCOL"], "HTTPS")>0?"https":"http") . "://" . $_SERVER["HTTP_HOST"] . truepath($_SERVER["REQUEST_URI"] . "/../");
        // Ensure trailing slash at the end
        if ($url[strlen($url)-1] != "/") { $url .= "/"; }
    }
    
    // Display menu
    echo "<div id='menu'>";
        echo "<div ".($step==1?"class='current'":"")."><h1>1.</h1><h2>".($step>1?__("language"):"")."</h2></div>";
        echo "<div ".($step==2?"class='current'":"")."><h1>2.</h1><h2>".($step>1?__("folders"):"")."</h2></div>";
        echo "<div ".($step==3?"class='current'":"")."><h1>3.</h1><h2>".($step>1?__("security"):"")."</h2></div>";
        echo "<div ".($step==4?"class='current'":"")."><h1>4.</h1><h2>".($step>1?__("database"):"")."</h2></div>";
        echo "<div ".($step==5?"class='current'":"")."><h1>5.</h1><h2>".($step>1?__("urls"):"")."</h2></div>";
    echo "</div>";
    
    // Display appropriate screen
    echo "<div id='content'>";
    if ($step == 1)
    {
        // Choose language
        echo "<h1>Welkom bij Wi3! Welcome to Wi3! </h1>";
        echo "<div id='welcomelogo'></div>";
        echo "<p>Kies alstublieft uw taal. Please pick your language. </p>";
        // NL
        echo "<form method='POST'>";
        echo "<input type='hidden' name='step' value='2' />";
        echo "<input type='hidden' name='lang' value='nl-nl' />";
        echo "<input type='submit' value='Nederlands' />";
        echo "</form>";
        // EN
        echo "<form method='POST'>";
        echo "<input type='hidden' name='step' value='2' />";
        echo "<input type='hidden' name='lang' value='en-us' />";
        echo "<input type='submit' value='English' />";
        echo "</form>";
    }
    else
    {        
        include("i18n/step" . $step ."_".$lang.".inc");
    }
    echo "</div>";
    
    include("i18n/footer_".$lang.".inc");
    
echo "</div>"; // End wrap
?>

</body>
</html>

<?

    /*

    // Multi language:

    I think the hybrid option is the best pick, but it needs some extra touches.

    Use the url with the language segment to set the page content language. If you add links to the other language pages, like wikipedia does, spiders will index them. Don't redirect, the language segment(s) can be dealt with in a parent controller, leaving the child specific segments for the child controller methods.

    Use a cookie to set the site language. If the user lands on an url without the language segment, the language in the cookie is the default language. If the user selects a language from the site language switch links, http://example.com/redirect/language/es-es, the language in the cookie is that value. If the user logs in and has set a site language in the profile, the language in the cookie is set to that language.

    This fulfills all the demands and keeps the language switching user-friendly.

    ---

    I'd also stick with the folder/param way. It's the most common and it does not require strange constructs for subdomains.

    I would not call it hybrid, but call it a distinct order to determine the most fitting language. My order looks like this (where the first one is the most important and the last one the least important):

    1. use language as given in the URL (which is also used for the language switch)
    2. if user is logged in, the language is taken from the profile setting
    3. if a cookie is present, the language is taken from there
    4. if URL does not contain a language piece and none above applies, evaluate the language from the language header sent from the browser
    5. finally fall back to default language

    I do only redirect if the plain domain is called. I want the language to be visible all the time because of canonical URLs.
    That's just my few cents ;)
    
    */
    
?>
