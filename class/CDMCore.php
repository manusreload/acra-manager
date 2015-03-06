<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cdmHelper
 *
 * @author manus
 */
require_once __DIR__ . '/MysqlConnector.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/App.php';
require_once __DIR__ . '/Constants.php';

class CDMCore {

    var $conn;

    public function __construct($mysql = true) {
        error_reporting(0);
        date_default_timezone_set("Europe/Madrid");
        $this->conn = new MysqlConnector();
        if ($mysql)
            $this->conn->connect();
    }

    public function isMysqlConnected() {
        return $this->conn->connected();
    }

    /**
     * Insert new user, and send an verification e-mail.
     * @param type $email
     * @param type $pass
     * @param type $name
     * @return 0 succees, -1 send email error, -2 email exists
     */
    public function registerUser($email, $pass, $name) {
        $email = mysql_real_escape_string($email);
        $pass = mysql_real_escape_string($pass);
        $name = mysql_real_escape_string($name);
        $verfication_code = $this->random_string(64);

        try {
            if ($this->conn->query("INSERT INTO `" . Constants::TABLE_USERS . "` (
            `" . Constants::USERS_COLUM_EMAIL . "`,
            `" . Constants::USERS_COLUM_PASS . "`,
            `" . Constants::USERS_COLUM_NAME . "`,
            `" . Constants::USERS_COLUM_VERFICATIONCODE . "`
                
            ) VALUES (
            '" . $email . "',
            '" . md5($pass) . "',
            '" . $name . "',
            '" . $verfication_code . "'
            )")) {
                $user = new User();
                $user->setData(array(Constants::USERS_COLUM_EMAIL => $email,
                    Constants::USERS_COLUM_PASS => md5($pass),
                    Constants::USERS_COLUM_NAME => $name,
                    Constants::USERS_COLUM_VERFICATIONCODE => $verfication_code,
                    Constants::USERS_COLUM_VERIFICATED => 0));
                if ($this->sendValidationMail($user)) {
                    return 0;
                } else {
                    return -1;
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return -2;
    }

    /**
     * 
     * @param User $user
     * @return boolean
     */
    public function sendValidationMail($user) {
        $message = "Hola " . $user->getName() . "!\nBienvenido a Device Cloud Manager, has solicitado crear una cuenta, y el proceso est&aacute; casi listo,
                    solo tienes que hacer click en este enlace: <a href=\"" . $_SERVER['HTTP_HOST'] . "/verification.php?code=" . $user->getVerificationCode() . "\">Validar cuenta</a>\nTambien puedes verificar tu cuenta accediendo e introducir: " . $user->getVerificationCode() . "\nAtentamente el Equipo CDM";
        $send_res = $this->send_mail($user->getEmail(), "Confirmar Cuenta", $message);
        return($send_res == "1");
    }

    private function validateToken($email, $token) {
        if ($token == "") {
            $token = $this->random_string(64, true, true);
            if (!$this->setToken($email, $token)) {
                $token = "";
            }
        }
        return $token;
    }

    private function setToken($email, $token) {
        return $this->conn->query("UPDATE `" . Constants::TABLE_USERS . "` SET `" . Constants::USERS_COLUM_TOKEN . "` = '" . $token . "' WHERE `" . Constants::USERS_COLUM_EMAIL . "` = '" . $email . "'");
    }

    public function getTokenByPass($email, $pass) {
        $email = mysql_real_escape_string($email);
        $pass = mysql_real_escape_string($pass);
        $res = $this->conn->query("SELECT * FROM `" . Constants::TABLE_USERS . "` WHERE 
            `" . Constants::USERS_COLUM_EMAIL . "` = '" . $email . "' AND 
            `" . Constants::USERS_COLUM_PASS . "` = '" . md5($pass) . "'");
        if ($res) {
            if ($row = mysql_fetch_array($res)) {

                return $this->validateToken($email, $row[Constants::USERS_COLUM_TOKEN]);
            }
        }
        return false;
    }

    /**
     * 
     * @param User $user
     */
    public function listApps($user) {
        $email = $user->getEmail();
        $res = $this->conn->query("SELECT * FROM `apps` WHERE 
            `author` = '" . $email . "' AND 
            `disabled` = '0'");
        $arr = array();
        while ($row = mysql_fetch_array($res)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * 
     * @param User $user
     * @return App Description
     */
    public function getApp($user, $id) {
        $email = $user->getEmail();
        $id = mysql_real_escape_string($id);

        $res = $this->conn->query("SELECT * FROM `apps` WHERE 
            `author` = '" . $email . "' AND 
            `appId` = '" . $id . "' AND 
            `disabled` = '0'");
        $arr = array();
        if ($row = mysql_fetch_array($res)) {
            $app = new App();
            $app->setData($row);
            return $app;
        }
    }

    /**
     * @return App Description
     */
    public function getAppDetails($id) {
        $id = mysql_real_escape_string($id);

        $res = $this->conn->query("SELECT * FROM `apps` WHERE 
            `appId` = '" . $id . "' AND 
            `disabled` = '0'");
        $arr = array();
        if ($row = mysql_fetch_array($res)) {
            $app = new App();
            $app->setData($row);
            return $app;
        }
        return false;
    }

    /**
     * 
     * @param App $app
     * @param string $regId
     * @return boolean
     */
    public function getClientDetails($app, $regId) {
        $regId = mysql_real_escape_string($regId);

        $res = $this->conn->query("SELECT * FROM `client` WHERE 
            `appId` = '" . $app->appId . "' AND 
            `registration` = '" . $regId . "'");
        $arr = array();
        if ($row = mysql_fetch_array($res)) {
            return true;
        }
        return false;
    }

    /**
     * 
     * @param User $user
     */
    public function removeApp($user, $id) {
        $id = mysql_real_escape_string($id);
        $res = $this->conn->query("UPDATE `apps` SET disabled = 1 WHERE 
                appId = '" . $id . "' AND
                author = '" . $user->getEmail() . "'");
        if (!$res) {
            echo mysql_error();
            return false;
        }
        return true;
    }
    
    /**
     * 
     * @param User $user
     */
    public function settingsApp($user, $id, $pkg, $error_pkg, $push) {
        $id = mysql_real_escape_string($id);
        $pkg = mysql_real_escape_string($pkg);
        $error_pkg = mysql_real_escape_string($error_pkg);
        $push = mysql_real_escape_string($push);
        $res = $this->conn->query("UPDATE `apps` SET pkg = '$pkg', error_pkg = '$error_pkg', push = '$push' WHERE 
                appId = '" . $id . "' AND
                author = '" . $user->getEmail() . "'");
        if (!$res) {
            echo mysql_error();
            return false;
        }
        return true;
    }
    /**
     * 
     * @param User $user
     */
    public function notifyApp($user, $id, $status) {
        $id = mysql_real_escape_string($id);
        $status = mysql_real_escape_string($status);
        $res = $this->conn->query("UPDATE `apps` SET notify = '$status' WHERE 
                appId = '" . $id . "' AND
                author = '" . $user->getEmail() . "'");
        if (!$res) {
            echo mysql_error();
            return false;
        }
        return true;
    }

    /**
     * 
     * @param User $user
     */
    public function newApp($user, $name, $pkg) {
        $name = mysql_real_escape_string($name);
        $pkg = mysql_real_escape_string($pkg);
        $res = $this->conn->query("INSERT INTO `apps` 
            (
                name,
                appId,
                secureId,
                pkg,
                author
            )
            VALUES
            (
                '" . $name . "',
                '" . $this->random_string(15, TRUE, FALSE, FALSE) . "',
                '" . $this->random_string(64, TRUE, TRUE, FALSE) . "',
                '" . $pkg . "',
                '" . $user->getEmail() . "'
            )");
        $arr = array();
        if (!$res)
            echo mysql_error();
        while ($row = mysql_fetch_array($res)) {
            $arr[] = $row;
        }
        return $arr;
    }

    public function loginUser($email, $token) {
        $email = mysql_real_escape_string($email);
        $token = mysql_real_escape_string($token);
        $res = $this->conn->query("SELECT * FROM `" . Constants::TABLE_USERS . "` WHERE 
            `" . Constants::USERS_COLUM_EMAIL . "` = '" . $email . "' AND 
            `" . Constants::USERS_COLUM_TOKEN . "` = '" . $token . "'");
        if ($res) {

            if ($row = mysql_fetch_array($res)) {
                return $row;
            }
        }
        return false;
    }
    
    public function getVersions($pkg)
    {
        $pkg = mysql_real_escape_string($pkg);
        
        $res = $this->conn->query("SELECT APP_VERSION_CODE,APP_VERSION_NAME, count(APP_VERSION_CODE) FROM reports WHERE PACKAGE_NAME = '" . $pkg . "' AND RESOLVED = 0 group by APP_VERSION_CODE");
        return $res;
    }
    
//    public function getBackups( $id)
//    {        
//        $res = $this->conn->query("SELECT * FROM backup WHERE app = '$id' ORDER BY date DESC");
//        return $res;
//    }
//    
//    public function insertBackup($appId, $date)
//    {
//        $this->conn->query("INSERT INTO backup (app,date) VALUES ('$appId', $date)");
//    }
//
//    function validateRegistration($reg) {
//        $reg = mysql_real_escape_string($reg);
//        $res = $this->conn->query("SELECT * FROM `client` WHERE 
//            `registration` = '" . $reg . "'");
//        if ($res) {
//
//            if ($row = mysql_fetch_array($res)) {
//                return true;
//            }
//        }
//        return false;
//    }
    /**
     * 
     * @param User $user
     */
    function verifyUser($user) {
        if ($user->isLogin()) {
            return $this->conn->query("UPDATE `" . Constants::TABLE_USERS . "` SET `" . Constants::USERS_COLUM_VERIFICATED . "` = 1 WHERE `" . Constants::USERS_COLUM_EMAIL . "` = '" . $user->getEmail() . "'");
        }
        return false;
    }

    function random_string($length = 10, $uc = TRUE, $n = TRUE, $sc = FALSE) {
        $source = 'abcdefghijklmnopqrstuvwxyz';
        if ($uc == 1)
            $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($n == 1)
            $source .= '1234567890';
        if ($sc == 1)
            $source .= '|@#~$%()=^*+[]{}-_';
        if ($length > 0) {
            $rstr = "";
            $source = str_split($source, 1);
            for ($i = 1; $i <= $length; $i++) {
                mt_srand((double) microtime() * 1000000);
                $num = mt_rand(1, count($source));
                $rstr .= $source[$num - 1];
            }
        }
        return $rstr;
    }
    
    function get_error_hash($stacktrace)
    {
        
        
    }
    
    function send_mail($email, $subject, $message, $autor = "Crash Reporting Team") {
        
        global  $config;
        if($config['smtp.google'])
        {
            
            require("phpmailer-gmail/class.phpmailer.php");
            require("phpmailer-gmail/class.smtp.php");

            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465;
            $mail->Username = $config['smtp.google.username'];
            $mail->Password = $config['smtp.google.password'];

            $mail->From = "no-reply@crashreporting.com";
            $mail->FromName = $autor;
            $mail->Subject = $subject;
            $mail->AltBody = $message;
            $mail->MsgHTML(nl2br($message));
            $mail->AddAddress($email);
            $mail->IsHTML(true);

            return $mail->Send();
        }
        else
        {
            return mail($email, $subject, $message);
        }
    }

}

?>
