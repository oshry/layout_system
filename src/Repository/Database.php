<?php
/**
 * Created by PhpStorm.
 * User: oshry
 * Date: 17/08/2016
 * Time: 4:38 PM
 */
/*
* Mysql database class - only one connection allowed
*/
namespace Repository;
class Database {
    private $_connection;
    private static $_instance; //The single instance
    private $_host = "localhost";
    private $_username = "root";
    private $_password = "oshry81";
    private $_database = "layout_system";
    /*
    Get an instance of the Database
    @return Instance
    */
    public static function getInstance() {
        if(!self::$_instance) { // If no instance then make one
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    // Constructor
    public function __construct() {
        $this->_connection = new \mysqli($this->_host, $this->_username,
            $this->_password, $this->_database);

        // Error handling
        if(mysqli_connect_error()) {
            trigger_error("Failed to conencto to MySQL: " . mysql_connect_error(),
                E_USER_ERROR);
        }
    }
    // Magic method clone is empty to prevent duplication of connection
    private function __clone() { }
    // Get mysqli connection
    public function getConnection() {
        return $this->_connection;
    }
}