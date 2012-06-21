<<<<<<< .mine
<?php
/**
 * Utility functions for generating URIs in HTML files
 *
 * @package Minify
 */

require_once dirname(__FILE__) . '/lib/Minify/HTML/Helper.php';


/*
 * Get an HTML-escaped Minify URI for a group or set of files. By default, URIs
 * will contain timestamps to allow far-future Expires headers.
 *
 * <code>
 * <link rel="stylesheet" type="text/css" href="<?= Minify_getUri('css'); ?>" />
 * <script src="<?= Minify_getUri('js'); ?>"></script>
 * <script src="<?= Minify_getUri(array(
 *      '//scripts/file1.js'
 *      ,'//scripts/file2.js'
 * )); ?>"></script>
 * </code>
 *
 * @param mixed $keyOrFiles a group key or array of file paths/URIs
 * @param array $opts options:
 *   'farExpires' : (default true) append a modified timestamp for cache revving
 *   'debug' : (default false) append debug flag
 *   'charset' : (default 'UTF-8') for htmlspecialchars
 *   'minAppUri' : (default '/min') URI of min directory
 *   'rewriteWorks' : (default true) does mod_rewrite work in min app?
 *   'groupsConfigFile' : specify if different
 * @return string
 */
function Minify_getUri($keyOrFiles, $opts = array())
{
    return Minify_HTML_Helper::getUri($keyOrFiles, $opts);
}


/**
 * Get the last modification time of several source js/css files. If you're
 * caching the output of Minify_getUri(), you might want to know if one of the
 * dependent source files has changed so you can update the HTML.
 *
 * Since this makes a bunch of stat() calls, you might not want to check this
 * on every request.
 * 
 * @param array $keysAndFiles group keys and/or file paths/URIs.
 * @return int latest modification time of all given keys/files
 */
function Minify_mtime($keysAndFiles, $groupsConfigFile = null)
{
    $gc = null;
    if (! $groupsConfigFile) {
        $groupsConfigFile = dirname(__FILE__) . '/groupsConfig.php';
    }
    $sources = array();
    foreach ($keysAndFiles as $keyOrFile) {
        if (is_object($keyOrFile)
            || 0 === strpos($keyOrFile, '/')
            || 1 === strpos($keyOrFile, ':\\')) {
            // a file/source obj
            $sources[] = $keyOrFile;
        } else {
            if (! $gc) {
                $gc = (require $groupsConfigFile);
            }
            foreach ($gc[$keyOrFile] as $source) {
                $sources[] = $source;
            }
        }
    }
    return Minify_HTML_Helper::getLastModified($sources);
}
=======
<?php
/**
 * Utility functions for generating group URIs in HTML files
 *
 * Before including this file, /min/lib must be in your include_path.
 * 
 * @package Minify
 */

require_once 'Minify/Build.php';


/**
 * Get a timestamped URI to a minified resource using the default Minify install
 *
 * <code>
 * <link rel="stylesheet" type="text/css" href="<?php echo Minify_groupUri('css'); ?>" />
 * <script type="text/javascript" src="<?php echo Minify_groupUri('js'); ?>"></script>
 * </code>
 *
 * If you do not want ampersands as HTML entities, set Minify_Build::$ampersand = "&" 
 * before using this function.
 *
 * @param string $group a key from groupsConfig.php
 * @param boolean $forceAmpersand (default false) Set to true if the RewriteRule
 * directives in .htaccess are functional. This will remove the "?" from URIs, making them
 * more cacheable by proxies.
 * @return string
 */ 
function Minify_groupUri($group, $forceAmpersand = false)
{
    $path = $forceAmpersand
        ? "/g={$group}"
        : "/?g={$group}";
    return _Minify_getBuild($group)->uri(
        '/' . basename(dirname(__FILE__)) . $path
        ,$forceAmpersand
    );
}


/**
 * Get the last modification time of the source js/css files used by Minify to
 * build the page.
 * 
 * If you're caching the output of Minify_groupUri(), you'll want to rebuild 
 * the cache if it's older than this timestamp.
 * 
 * <code>
 * // simplistic HTML cache system
 * $file = '/path/to/cache/file';
 * if (! file_exists($file) || filemtime($file) < Minify_groupsMtime(array('js', 'css'))) {
 *     // (re)build cache
 *     $page = buildPage(); // this calls Minify_groupUri() for js and css
 *     file_put_contents($file, $page);
 *     echo $page;
 *     exit();
 * }
 * readfile($file);
 * </code>
 *
 * @param array $groups an array of keys from groupsConfig.php
 * @return int Unix timestamp of the latest modification
 */ 
function Minify_groupsMtime($groups)
{
    $max = 0;
    foreach ((array)$groups as $group) {
        $max = max($max, _Minify_getBuild($group)->lastModified);
    }
    return $max;
}

/**
 * @param string $group a key from groupsConfig.php
 * @return Minify_Build
 * @private
 */
function _Minify_getBuild($group)
{
    static $builds = array();
    static $gc = false;
    if (false === $gc) {
        $gc = (require dirname(__FILE__) . '/groupsConfig.php');
    }
    if (! isset($builds[$group])) {
        $builds[$group] = new Minify_Build($gc[$group]);
    }
    return $builds[$group];
}
>>>>>>> .r63677
