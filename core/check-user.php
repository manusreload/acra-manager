<?php

/**
 * Utiliza este archivo para comprobar el login, en caso de que no este logeado ira al login.
 * En caso de que la cuenta no haya sido verificada ira a la verificacion.
 */
if (haveLogin()) {
//    if (!getUser()->isVerificated()) {
//        if (!strpos($_SERVER['SCRIPT_NAME'], 'verification.php')) {
//            header("Location: verification.php");
//            return;
//        }
//    }
} else {

    if (!strpos($_SERVER['SCRIPT_NAME'], 'login.php')) {
        if (strpos($_SERVER['REQUEST_URI'], 'action=do_logout')) {
            header("Location: login.php");
        } else {
            header("Location: login.php?goto=" . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        }
        die();
    }
}
?>
