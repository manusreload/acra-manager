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
        return connected;
    }
    //Tested :D
    function connect() {
        global $config;
        $this->addr = $config['database.host'];
        $this->user = $config['database.user'];
        $this->pass = $config['database.pass'];
        $this->name = $config['database.name'];

        $result = $this->mysql_conn = mysql_connect($this->addr, $this->user, $this->pass);
        if ($result) {
            return ($this->connected = mysql_select_db($this->name, $this->mysql_conn));
        }
        return false;
    }

    function query($query = "") {
        $res = mysql_query($query, $this->mysql_conn);
        if (!$res) {
            throw new Exception(mysql_error($this->mysql_conn));
        }
        return $res;
    }

    function close() {
        mysql_close($this->mysql_conn);
        return true;
    }

}

?>
