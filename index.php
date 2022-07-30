<?php
require_once('./lib/boot.php');

if(defined('URL_DIR'))
    $dir = URL_DIR;
else
    $dir = dirname($_SERVER['PHP_SELF']);

if($dir == '/') $dir = '';

$__SECTS = explode('/',str_replace($dir, '', $_SERVER['REQUEST_URI']));
$controller_dir = APP_PATH.'/src/Controllers/';
$controller_found = false;
$sep = '';

$__ACTION = $__ACT_ID = null;
$__SCRIPT = 'home.php';

if(empty($__SECTS[1])){
    $controller_found = true;
    $__SECTION = 'home';
    $__SCRIPT = $__SECTION.'.php';
}else{
    for($idx= 1, $count = count($__SECTS); $idx<$count; $idx++){
        $__SECTION = $sep.$__SECTS[$idx];
        if(is_file($__SECTION.'.php') && !$controller_found){
            $controller_found = true;

            $__ACTION = $__SECTS[$idx + 1];
            $__ACT_ID = $__SECTS[$idx + 2];
            $code =  $__SECTS[$idx + 3];
            $__SCRIPT = $__SECTION.'.php';
            $idx = $idx + 2;
            //break;
        }else if($controller_found){
           $__SECTS[$idx] = str_replace('_',' ',$__SECTS[$idx]);
        }
        $sep = '/';
    }
}

//$__SCRIPT = "maintenance.php";

define('__PATH', implode('/', $__SECTS));
define('__SECTION', $__SECTION);
define('__SCRIPT', $__SCRIPT);

//die($__ACTION   );

if($controller_found){
	include_once($controller_dir.$__SCRIPT);
}else{
    header('HTTP/1.0 404 Not Found');
	//include_once('404.php');
}
