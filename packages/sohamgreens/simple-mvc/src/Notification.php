<?php
namespace SohamGreens\SimpleMvc;

class Notification {

    const INFO = 1;
    const ERROR = 2;
    const WARNING = 3;

    private function __construct() {
        ;
    }

    public static function set($message, $type = self::INFO) {
        $notification['message'] = $message;
        $notification['type'] = $type;

        $_SESSION['__NOTIFICATION'] = $notification;
    }

    public static function error($message) {
        self::set($message, self::ERROR);
    }
    
    public static function warning($message) {
        self::set($message, self::WARNING);
    }

    public static function get() {
        if (!isset($_SESSION['__NOTIFICATION']))
            return;

        $notification = $_SESSION['__NOTIFICATION'];
        unset($_SESSION['__NOTIFICATION']);
        return $notification;
    }

}
