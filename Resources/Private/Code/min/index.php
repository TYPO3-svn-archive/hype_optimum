<<<<<<< .mine
<?php
/**
 * Front controller for default Minify implementation
 * 
 * DO NOT EDIT! Configure this utility via config.php and groupsConfig.php
 * 
 * @package Minify
 */

define('MINIFY_MIN_DIR', dirname(__FILE__));

// load config
require MINIFY_MIN_DIR . '/config.php';

// setup include path
set_include_path($min_libPath . PATH_SEPARATOR . get_include_path());

require 'Minify.php';

Minify::$uploaderHoursBehind = $min_uploaderHoursBehind;
Minify::setCache(
    isset($min_cachePath) ? $min_cachePath : ''
    ,$min_cacheFileLocking
);

if ($min_documentRoot) {
    $_SERVER['DOCUMENT_ROOT'] = $min_documentRoot;
} elseif (0 === stripos(PHP_OS, 'win')) {
    Minify::setDocRoot(); // IIS may need help
}

$min_serveOptions['minifierOptions']['text/css']['symlinks'] = $min_symlinks;
// auto-add targets to allowDirs
foreach ($min_symlinks as $uri => $target) {
    $min_serveOptions['minApp']['allowDirs'][] = $target;
}

if ($min_allowDebugFlag) {
    if (! empty($_COOKIE['minDebug'])) {
        foreach (preg_split('/\\s+/', $_COOKIE['minDebug']) as $debugUri) {
            if (false !== strpos($_SERVER['REQUEST_URI'], $debugUri)) {
                $min_serveOptions['debug'] = true;
                break;
            }
        }
    }
    // allow GET to override
    if (isset($_GET['debug'])) {
        $min_serveOptions['debug'] = true;
    }
}

if ($min_errorLogger) {
    require_once 'Minify/Logger.php';
    if (true === $min_errorLogger) {
        require_once 'FirePHP.php';
        $min_errorLogger = FirePHP::getInstance(true);
    }
    Minify_Logger::setLogger($min_errorLogger);
}

// check for URI versioning
if (preg_match('/&\\d/', $_SERVER['QUERY_STRING'])) {
    $min_serveOptions['maxAge'] = 31536000;
}
if (isset($_GET['g'])) {
    // well need groups config
    $min_serveOptions['minApp']['groups'] = (require MINIFY_MIN_DIR . '/groupsConfig.php');
}
if (isset($_GET['f']) || isset($_GET['g'])) {
    // serve!   

    if (! isset($min_serveController)) {
        require 'Minify/Controller/MinApp.php';
        $min_serveController = new Minify_Controller_MinApp();
    }
    Minify::serve($min_serveController, $min_serveOptions);
        
} elseif ($min_enableBuilder) {
    header('Location: builder/');
    exit();
} else {
    header("Location: /");
    exit();
}=======
<?php
/**
 * Front controller for default Minify implementation
 * 
 * DO NOT EDIT! Configure this utility via config.php and groupsConfig.php
 * 
 * @package Minify
 */

define('MINIFY_MIN_DIR', dirname(__FILE__));

// load config
require MINIFY_MIN_DIR . '/config.php';

// setup include path
set_include_path($min_libPath . PATH_SEPARATOR . get_include_path());

require 'Minify.php';

Minify::$uploaderHoursBehind = $min_uploaderHoursBehind;
Minify::setCache(
    isset($min_cachePath) ? $min_cachePath : ''
    ,$min_cacheFileLocking
);

if ($min_documentRoot) {
    $_SERVER['DOCUMENT_ROOT'] = $min_documentRoot;
} elseif (0 === stripos(PHP_OS, 'win')) {
    Minify::setDocRoot(); // IIS may need help
}

$min_serveOptions['minifierOptions']['text/css']['symlinks'] = $min_symlinks;

if ($min_allowDebugFlag && isset($_GET['debug'])) {
    $min_serveOptions['debug'] = true;
}

if ($min_errorLogger) {
    require_once 'Minify/Logger.php';
    if (true === $min_errorLogger) {
        require_once 'FirePHP.php';
        Minify_Logger::setLogger(FirePHP::getInstance(true));
    } else {
        Minify_Logger::setLogger($min_errorLogger);
    }
}

// check for URI versioning
if (preg_match('/&\\d/', $_SERVER['QUERY_STRING'])) {
    $min_serveOptions['maxAge'] = 31536000;
}
if (isset($_GET['g'])) {
    // well need groups config
    $min_serveOptions['minApp']['groups'] = (require MINIFY_MIN_DIR . '/groupsConfig.php');
}
if (isset($_GET['f']) || isset($_GET['g'])) {
    // serve!   
    Minify::serve('MinApp', $min_serveOptions);
        
} elseif ($min_enableBuilder) {
    header('Location: builder/');
    exit();
} else {
    header("Location: /");
    exit();
}
>>>>>>> .r63677
