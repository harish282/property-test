<?php
namespace SohamGreens\SimpleMvc;

class Session {


    private function __construct() {
        ;
    }

    static public function start() {
        session_start();

    }

    public static function is_set($key) {
        return isset($_SESSION[$key]);
    }

    public static function isEmpty($key) {
        return empty($_SESSION[$key]);
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        $value =  isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
        return $value;
    }

    public static function setArray($keys, $value) {
        $fkey = array_shift($keys);
        $fdata = self::get($fkey);
        $data = &$fdata;
        foreach ($keys as $key) {
            echo $key . "<br>";
            if (isset($data[$key])) {
                $data = &$data[$key];
            } else {
                $data[$key] = array();
                $data = &$data[$key];
            }
        }
        $data = $value;
        self::set($fkey, $fdata);
    }

    public static function destroy() {
        ob_end_clean();
        $_SESSION = array();
        session_unset();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }

    public static function fixObject(&$object) {
        if (!is_object($object) && gettype($object) == 'object')
            return ($object = unserialize(serialize($object)));
        return $object;
    }

}
