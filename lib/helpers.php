<?php

use SohamGreens\SimpleMvc\Url;
use SohamGreens\SimpleMvc\View;

if(!function_exists('env')){
    function env($key){
        return $_ENV[$key];
    }
}

if(!function_exists('session')){
    function session($key, $value=null){
        if (!empty($value))
            SohamGreens\SimpleMvc\Session::set($key, $value);
        else
            $value = SohamGreens\SimpleMvc\Session::get($key);
        return $value;
    }
}

if(!function_exists('url_site')){
    function url_site($url, $onlyHttp=false){
        return Url::site($url, $onlyHttp);
    }
}

if(!function_exists('view')){
    function view($view){
        return View::instance($view);
    }
}