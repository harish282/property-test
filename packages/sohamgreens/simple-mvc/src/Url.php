<?php
namespace SohamGreens\SimpleMvc;

class Url {

    static public function site($url = '', $onlyHttp = false) {
        return preg_match('/^(http|\/\/)/', $url) ? $url : (($onlyHttp ? str_replace('https://', 'http://', URL_SITE) : URL_SITE) . str_replace(' ', '_', $url));
    }

    static public function siteSSL($url = '') {
        return str_replace('http://', 'https://', URL_SITE) . str_replace(' ', '_', $url);
    }

    static public function sendHeader($http_equiv, $content = "") {
        if (!headers_sent()) {
            header($http_equiv . ":" . $content);
        } else {
            echo "<meta http-equiv='$http_equiv' content='$content'>";
        }
    }

    static public function redirect($url) {
        if (strpos($url, 'http') === false) {
            if (__MOBILE && strpos($url, 'mobile/') === false) {
                $url = self::site('mobile/' . $url);
            } else {
                $url = self::site($url);
            }
        }

        if (!headers_sent()) {
            header("location:$url");
        } else {
            echo "<meta http-equiv='refresh' content='0;url=$url'>";
        }

        //Utility::sendHeader('location', $url);
        exit();
    }

    static public function getSiteUrl() {

        /** Browser protocol */
        if ($_SERVER ["HTTPS"] != "on" && ($_SERVER ["SERVER_PORT"] != "443"))
            $_HTTP = 'http://';
        else
            $_HTTP = 'https://';

        $_PORT = ($_SERVER ['SERVER_PORT'] == 80 || $_SERVER ['SERVER_PORT'] == 443) ? '' : ":" . $_SERVER ['SERVER_PORT'];
        $_SERVER_NAME = $_SERVER ['SERVER_NAME'];

        $_SELF_DIR = (dirname($_SERVER ['SCRIPT_NAME']) == ".") ? '' : dirname($_SERVER ['SCRIPT_NAME']);

        $_SELF_DIR = str_replace(DIRECTORY_SEPARATOR, '/', $_SELF_DIR);

        if (substr($_SELF_DIR, - 1) != "/")
            $_SELF_DIR .= "/";

        return $_HTTP . $_SERVER_NAME . $_PORT . $_SELF_DIR;
    }

    static public function getController() {
        $urlVars = explode('/', $_SERVER['REQUEST_URI']);
        return $urlVars[1];
    }

    static public function isValidUrl($url) {
        // first do some quick sanity checks:
        if (!$url || !is_string($url)) {
            return false;
        }
        // quick check url is roughly a valid http request: ( http://blah/... ) 
        if (!preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url)) {
            return false;
        }
        // the next bit could be slow:
        if (self::getHttpResponseCode($url) != 200) {
//      if(getHttpResponseCode_using_getheaders($url) != 200){  // use this one if you cant use curl
            return false;
        }
        // all good!
        return true;
    }

    static public function getHttpResponseCode($url, $followredirects = true) {
        // returns int responsecode, or false (if url does not exist or connection timeout occurs)
        // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
        // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
        // if $followredirects == true : return the LAST  known httpcode (when redirected)
        if (!$url || !is_string($url)) {
            return false;
        }
        $ch = curl_init($url);
        if ($ch === false) {
            return false;
        }
        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true);    // dont need body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // catch output (do NOT print!)
        if ($followredirects) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);  // fairly random number, but could prevent unwanted endless redirects with followlocation=true
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        }
//      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);   // fairly random number (seconds)... but could prevent waiting forever to get a result
//      curl_setopt($ch, CURLOPT_TIMEOUT        ,6);   // fairly random number (seconds)... but could prevent waiting forever to get a result
//      curl_setopt($ch, CURLOPT_USERAGENT      ,"Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1");   // pretend we're a regular browser
        curl_exec($ch);
        if (curl_errno($ch)) {   // should be 0
            curl_close($ch);
            return false;
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // note: php.net documentation shows this returns a string, but really it returns an int
        curl_close($ch);
        return $code;
    }

    static public function exists($url) {
        return self::isValidUrl($url);
    }

}
