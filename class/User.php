<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author manus
 */
require_once 'CDMCore.php';
class User {
    var $name, $email, $pass, $token, $code;
    var $core;
    var $level; // only for administration propouses
    private $login;
    private $verificated;

    public function __construct() {
        $this->login = false;
        $this->core = new CDMCore();
    }

    public function login($email, $token) {
        $res = $this->core->loginUser($email, $token);
        if (is_array($res)) {
            //Load some data for the user
            $this->setData($res);
            //Make the user loged in
            $this->login = true;
            $this->token = $token;
            return true;
        }
    }

    public function setData($arr) {
        $this->email = $arr[Constants::USERS_COLUM_EMAIL];
        $this->name = $arr[Constants::USERS_COLUM_NAME];
        $this->pass = $arr[Constants::USERS_COLUM_PASS];
        $this->level = $arr['level'];
        $this->code = $arr[Constants::USERS_COLUM_VERFICATIONCODE];
        $this->verificated = $arr[Constants::USERS_COLUM_VERIFICATED] == 0 ? false : true;
    }

    public function isLogin() {
        return $this->login;
    }

    public function isVerificated() {
        return $this->verificated;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getVerificationCode() {
        return $this->code;
    }

    public function getName() {
        return $this->name;
    }
    public function getLevel() {
        return $this->level;
    }

    public function verify($code) {
        if ($code == $this->code) {
            if ($this->core->verifyUser($this)) {
                $this->verificated = true;
                return true;
            }
        }
        return false;
    }

    function sendVerificationMail() {
        return $this->core->sendValidationMail($this);
    }

    public function getApps() {
        return $this->core->listApps($this);
    }
    /**
     * 
     * @param type $id
     * @return App
     */
    public function getApp($id) {
        return $this->core->getApp($this, $id);
    }
    
    public function createApp($name, $pkg) {
        return $this->core->newApp($this, $name, $pkg);
    }
    public function removeApp($name) {
        return $this->core->removeApp($this, $name);
    }
    
    public function changeSettings($app, $pkg, $error_pkg, $push)
    {
        return $this->core->settingsApp($this, $app, $pkg, $error_pkg, $push);
    }
    public function notify($app, $status)
    {
        return $this->core->notifyApp($this, $app, $status);
    }


}

?>
