<?php

use SohamGreens\SimpleMvc\DB;
use SohamGreens\SimpleMvc\Session;

if(!defined('URL_SITE')) define('URL_SITE', '');

$dotenv = Dotenv\Dotenv::createImmutable(APP_PATH);
$dotenv->load();

Session::start();


try {
    $db = new DB(env('DB_HOST'), env('DB_USER'), env('DB_PWD'), env('DB_NAME'));
} catch (\SQLException $e) {
    dd($e);
    exit();
}