<?php
require_once "core/auth.php";
require_once "core/check-user.php";
if (!haveLogin()) {
    header("Location: login.php");
} else {
    $app = getUser()->getApp($_REQUEST['app']);
    if(isset($_REQUEST['time']))
    {
        if(!$app->download_backup($_REQUEST['time'], $_REQUEST['item']))
        {
            echo "File not found!";
        }
        exit;
    }
    
}
    echo "error!";
?>
