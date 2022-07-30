<?php
namespace SohamGreens\SimpleMvc;

final class Utility {

    static public function print_a($var) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    static public function sendHeader($http_equiv, $content = "") {
        if (!headers_sent())
            header($http_equiv . ":" . $content);
        else
            echo "<meta HTTP-EQUIV=$http_equiv CONTENT ='$content'>";
    }

    static public function setCookie($cname, $cvalue = "", $cexp = "", $cpath = "", $cdomain = "", $csec = "") {
        if (!headers_sent())
            setcookie($cname, $cvalue, $cexp, $cpath, $cdomain, $csec);
        else {
            if (!empty($cexp))
                $content .= "expires=" . gmdate("D, d M Y H:i:s", $cexp) . " GMT; ";
            if (!empty($cpath))
                $content .= "path=" . $cpath . "; ";
            if (!empty($cdomain))
                $content .= "domain=" . $cdomain . "; ";
            if ($csec)
                $content .= "secure;";
            echo "<meta HTTP-EQUIV=Set-Cookie CONTENT='$cname=$cvalue;content' >";
        }
    }

    static public function redirect($url) {
        header('location:' . $url);
        exit();
    }

    static public function getSiteUrl() {

        /** Browser protocol */
        if (@$_SERVER ["HTTPS"] != "on" && (@$_SERVER ["SERVER_PORT"] != "443"))
            $_HTTP = 'http://';
        else
            $_HTTP = 'https://';

        $_PORT = ($_SERVER ['SERVER_PORT'] == 80 || $_SERVER ['SERVER_PORT'] == 443) ? '' : ":" . $_SERVER ['SERVER_PORT'];
        $_SERVER_NAME = $_SERVER ['SERVER_NAME'];

        $_SELF_DIR = (dirname($_SERVER ['PHP_SELF']) == ".") ? '' : dirname($_SERVER ['PHP_SELF']);

        if (substr($_SELF_DIR, - 1) != "/")
            $_SELF_DIR .= "/";

        return $_HTTP . $_SERVER_NAME . $_PORT . $_SELF_DIR;
    }

    public static function preserve($data) {
        $_SESSION ['__PRESERVED'] = $data;
    }

    public static function getPreserved() {
        if (!isset($_SESSION ['__PRESERVED']))
            return;

        $data = $_SESSION ['__PRESERVED'];
        unset($_SESSION ['__PRESERVED']);
        return $data;
    }

    public static function objectToArray($data) {
        if (function_exists('get_object_vars'))
            return get_object_vars($data);
        elseif (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result [$key] = self::objectToArray($value);
            }
            return $result;
        }
        return $data;
    }

    public static function convertToTime($date) {
        return strtotime(preg_replace('/(\d+)\/(\d+)\/(\d+)/', '${2}/${1}/${3}', $date));
    }

    public static function get_all_files($parent_dir = "", $file_type = "", $include_sub_dir = true, &$dir_arr = NULL, &$file_arr = NULL) {

        if (!is_array($file_arr))
            $file_arr = array();
        if (is_dir($parent_dir)) {
            $file_type = strtolower($file_type);
            if (!preg_match("/\/$/", $parent_dir))
                $parent_dir .= "/";
            if ($dh = opendir($parent_dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (is_dir($parent_dir . $file) && $file != "." && $file != "..") {
                        $dir_arr[] = $parent_dir . $file;
                        if ($include_sub_dir)
                            $sub_dir = self::get_all_files($parent_dir . $file, $file_type, $include_sub_dir, $dir_arr, $file_arr);
                    }
                    elseif (is_file($parent_dir . $file) && $file != "." && $file != "..") {

                        $path_parts = pathinfo($file);
                        $ext = $path_parts["extension"];
                        if (!isset($ext) || trim($ext) == "")
                            $ext = "12356";
                        if (strstr($file_type, strtolower($ext)) || $file_type == "")
                            $file_arr[] = $parent_dir . $file;
                    }
                }
                closedir($dh);
            }
            arsort($file_arr);
            return $file_arr;
        }
        return 0;
    }

    static public function randomString($length = 12, $type = 'mixed') {

        if ($type == 'string') {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        } elseif ($type == 'num' || $type == 'number') {
            $chars = '1234567890';
        } else {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        }

        $charlen = strlen($chars);

        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $charlen - 1);
            $str .= $chars[$rand];
        }

        return $str;
    }

    static public function delDir($dir) {
        if (!is_dir($dir))
            $dir = dirname($dir);
        $file_arr = self::get_all_files($dir, "", true, $dir_arr);
        for ($i = 0; $i < count($file_arr); $i++)
            @unlink($file_arr[$i]);
        for ($i = 0; $i < count($dir_arr); $i++)
            @rmdir($dir_arr[$i]);
        @rmdir($dir);
    }

    function timeLeft($integer) {
        $seconds = $integer;
        if ($seconds / 60 >= 1) {
            $minutes = floor($seconds / 60);
            if ($minutes / 60 >= 1) { # Hours  
                $hours = floor($minutes / 60);
                if ($hours / 24 >= 1) { #days  
                    $days = floor($hours / 24);
                    if ($days / 7 >= 1) { #weeks  
                        $weeks = floor($days / 7);
                        if ($weeks >= 2)
                            $return = "$weeks Weeks";
                        else
                            $return = "$weeks Week";
                    } #end of weeks  
                    $days = $days - (floor($days / 7)) * 7;
                    if ($weeks >= 1 && $days >= 1)
                        $return = "$return, ";
                    if ($days >= 2)
                        $return = "$return $days days";
                    if ($days == 1)
                        $return = "$return $days day";
                } #end of days 
                $hours = $hours - (floor($hours / 24)) * 24;
                if ($days >= 1 && $hours >= 1)
                    $return = "$return, ";
                if ($hours >= 2)
                    $return = "$return $hours hours";
                if ($hours == 1)
                    $return = "$return $hours hour";
            } #end of Hours 
            $minutes = $minutes - (floor($minutes / 60)) * 60;
            if ($hours >= 1 && $minutes >= 1)
                $return = "$return, ";
            if ($minutes >= 2)
                $return = "$return $minutes minutes";
            if ($minutes == 1)
                $return = "$return $minutes minute";
        } #end of minutes  
        $seconds = $integer - (floor($integer / 60)) * 60;
        if ($minutes >= 1 && $seconds >= 1)
            $return = "$return, ";
        if ($seconds >= 2)
            $return = "$return $seconds seconds";
        if ($seconds == 1)
            $return = "$return $seconds second";
        $return = "$return.";
        return $return;
    }

    static public function getHours($integer) {
        return $integer / 3600;
    }

    static public function getMinutes($integer) {
        return $integer / 60;
    }

    static public function getHoursMinutesSeconds($integer) {

        $h = intVal($integer / 3600);
        $integer = intVal($integer % 3600);

        $m = intVal($integer / 60);
        $s = intVal($integer % 60);

        return array('h' => $h, 'm' => $m, 's' => $s);
    }

    //date formate should be mm/dd/YYYY
    static public function calculateage($birthday) {

        list($month, $day, $year) = explode("/", $birthday);
        $current_year = date("Y");

        $year_diff = $current_year - $year;
        $month_diff = date("m") - $month;
        $day_diff = date("d") - $day;

        if ($month_diff < 0)
            $year_diff--;
        elseif ($day_diff < 0 && $month_diff == 0)
            $year_diff--;

        return $year_diff;
    }

    static public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    static public function mail($to, $subject, $mailbody, $type = '') {
        if (!is_array($to))
            $to = array($to);
        SMail::loadswift();

        $transport = Swift_MailTransport::newInstance();
        $mailer = Swift_Mailer::newInstance($transport);
        $message = Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom(array(FROM_EMAIL => FROM_NAME));
        $message->setTo($to);
        $message->setBody($mailbody, $type);
        return $mailer->send($message);
    }

    static public function xss_clean($data) {
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }

}
