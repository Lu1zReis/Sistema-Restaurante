<?php
namespace conn;
class Conexão {
    private static $instance;
    
    public static function getConn() {
        if (!isset(self::$instance)) {
            self::$instance = new \PDO ('mysql:host=localhost; dbname=restaurante; charset=utf8', 'root', '');
        }
        return self::$instance;
    }

}