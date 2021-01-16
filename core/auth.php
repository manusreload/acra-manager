<?php

/**
 * How to use:
 *  1. include core/auth.php
 *      2. For check login if are user loged use: haveLogin();
 *      3. For get the user use: getUser();
 */

session_start();
require_once __DIR__ . '/../class/User.php';

//Chech request login:
if (isset($_GET['action']) && $_GET['action'] == 'do_login') {
    //Test login, and store result in: $GLOBALS['login_result']
    if (isset($_POST['email']) && isset($_POST['pass'])) {
        $core = new CDMCore();
        $token = $core->getTokenByPass($_POST['email'], $_POST['pass']);
        if (!empty($token)) {
            $_SESSION['logeduser'] = $_POST['email'];
            $_SESSION['logedpass'] = $token;
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'do_logout') {
    $GLOBALS['isLogin'] = false;
    unset($_SESSION['logedpass']);
    unset($_SESSION['logeduser']);
    unset($_SESSION['device']);
}

if (isset($_SESSION['logeduser']) && isset($_SESSION['logedpass'])) {
    $user = new User();
    if ($user->login($_SESSION['logeduser'], $_SESSION['logedpass'])) {
        $GLOBALS['logeduser_obj'] = $user;
        $GLOBALS['isLogin'] = true;
        $GLOBALS['login_result'] = 1;
    } else {
        $GLOBALS['login_result'] = 0;
        $GLOBALS['isLogin'] = false;
        unset($_SESSION['logedpass']);
        unset($_SESSION['logeduser']);
    }
}

if (!function_exists("haveLogin")) {

    /**
     * Si hay login devuelve true, en caso contrario false
     * @return boolean
     */
    function haveLogin() {
        return (isset($GLOBALS['isLogin']) && $GLOBALS['isLogin']);
    }

}

if (!function_exists("getUser")) {

    /**
     * 
     * @return User
     */
    function getUser() {
        if (isset($GLOBALS['logeduser_obj'])) {
            return $GLOBALS['logeduser_obj'];
        }
    }

}
?>
