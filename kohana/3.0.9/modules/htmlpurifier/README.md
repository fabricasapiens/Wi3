# Purifier for Kohana

Overloads [Security::xss_clean](http://kohanaframework.org/guide/api/Security#xss_clean) to provide secure XSS filtering using [HTML Purifier](http://htmlpurifier.org/).

# Installing Purifier

### Using Git

If your application is in a Git repository, you can simply add this repository as a submodule:

    git submodule add git://github.com/shadowhand/purifier.git modules/purifier
    git submodule update --init

If you want to use a specific version, you can check out the tag of that version:

    cd modules/purifier
    git checkout v0.1.0
    cd -
    git add modules/purifier

Always remember to commit changes you make to submodules!

    git commit -m 'Added Purifier module'

To install HTML Purifier, you will need to go into the purifier module directory and download it:

    cd modules/purifier
    git submodule update --init

*HTML Purifier is enabled as submodule of the Purifier module. Submodules of submodules are not automatically initialized!*

### FTP or Plain Files

For an untracked repository, you can [download](http://github.com/shadowhand/purifier/archives/master) the repository and install it to `MODPATH/purifier`. To download a specific version, select the tag on Github before clicking the download link.

You will also need to [download HTML Purifier](http://htmlpurifier.org/download) and install the entire "htmlpurifier" directory to `MODPATH/purifier/vendor/htmlpurifier`.

## After Installation

After HTML Purifier is installed, you will need to make the `library/DefinitionCache/Serializer` in `MODPATH/purifier/vendor/htmlpurifier` writable by the web server.

# Using Purifier

To use Purifier, just call [Security::xss_clean](http://kohanaframework.org/guide/api/Security#xss_clean) as you normally would. HTML Purifier will be used instead of the default "Bitflux" filter.

## Advanced Usage

If you want to access HTMLPurifier directly:

    $html = Security::htmlpurifier();

You can configure the [HTMLPurifier settings](http://htmlpurifier.org/live/configdoc/plain.html) by creating `APPPATH/config/purifier.php`:

    return array(
        'settings' => array(
            ... => ...
        ),
    );
