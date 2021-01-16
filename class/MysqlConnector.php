<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mysqlConnector
 *
 * @author manus
 */
require_once __DIR__ . '/../config/config.inc.php';

class MysqlConnector {

    var $mysql_conn;
    var $connected;
    function __construct() {
        $this->connected = false;
    }
    function connected()
    {
        return $this->connected;
    }
    //Tested :D
    function connect() {
        global $config;
        $this->addr = $config['database.host'];
        $this->user = $config['database.user'];
        $this->pass = $config['database.pass'];
        $this->name = $config['database.name'];

        $result = $this->mysql_conn = mysqli_connect($this->addr, $this->user, $this->pass);
        if ($result) {
            return ($this->connected = mysqli_select_db($this->mysql_conn, $this->name));
        }
        return false;
    }

    function query($query = "") {
        $res = mysqli_query($this->mysql_conn, $query);
        if (!$res) {
            $error = mysqli_error($this->mysql_conn);
            echo $error;
            throw new Exception($error);
        }
        return $res;
    }

    function close() {
        mysqli_close($this->mysql_conn);
        return true;
    }

}
