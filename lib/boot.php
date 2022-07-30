<?php
define('APP_PATH', dirname(dirname(__FILE__)));

require APP_PATH . '/vendor/autoload.php';

if (!defined('DEBUG'))
    define('DEBUG', true);
if (!defined('DIR_LOG'))
    define('DIR_LOG', APP_PATH . '/logs');
if (!defined('DIR_LIB'))
    define('DIR_LIB', APP_PATH . '/lib');


/* * set error handler function * */

function handle_error($errno, $errstr, $errfile, $errline) {
    $replevel = error_reporting();
    if (( $errno & $replevel ) != $errno)
        return;

    $errmsg = "Error($errno): $errstr at line $errline in file $errfile";
    if (DEBUG) {
        dd($errmsg);
        return;
    } elseif (ob_get_level() > 0){
        ob_end_clean();
    }
    
    if(!is_dir(DIR_LOG)) mkdir(DIR_LOG, 0775, true);

    $fp = fopen(DIR_LOG . '/error.txt', 'a+');
    fwrite($fp, $errmsg . "\n");
    fclose($fp);
    exit;
}

function handle_exception($exception) {
    if (DEBUG) {
        dd($exception);
        return;
    } elseif (ob_get_level() > 0){
        ob_end_clean();
    }
        
    if(!is_dir(DIR_LOG)) mkdir(DIR_LOG, 0775, true);

    $fp = fopen(DIR_LOG . '/exceptions.txt', 'a+');
    fwrite($fp, $exception->getMessage() . "\n");
    fclose($fp);
    exit;
}

set_error_handler("handle_error", E_ALL & ~E_NOTICE); //Set error handler
set_exception_handler("handle_exception"); //Set exception handler


require_once(APP_PATH.'/config/config.php');
