<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Include The Compiled Class File
|--------------------------------------------------------------------------
|
| To dramatically increase your application's performance, you may use a
| compiled class file which contains all of the classes commonly used
| by a request. The Artisan "optimize" is used to create this file.
|
*/

$compiledPath = __DIR__.'/cache/compiled.php';

if (file_exists($compiledPath)) {
    require $compiledPath;
}

if (function_exists('pinba_script_name_set') && !empty($_SERVER['REQUEST_URI'])) {
    pinba_script_name_set($_SERVER['REQUEST_URI']);
}
if (function_exists('pinba_hostname_set')) {
    pinba_hostname_set('php7');
}

ini_set('xdebug.default_enable', '1');
ini_set('xdebug.overload_var_dump', '1');
ini_set('xdebug.var_display_max_data', '1255');
ini_set('xdebug.var_display_max_depth', '50');
ini_set('xdebug.var_display_max_children', '128');