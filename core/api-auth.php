<?php

$request = array("email", "token", "device_id");

/**
 * Description of remoteRegistration
 *
 * @author manus
 */
require_once '../class/User.php';
$checked = true;
foreach ($request as $param) {
    if (!isset($_POST[$param])) {
        $checked = false;
        break;
    }
}
if (!$checked) {
    $data['resutl'] = "error";
    $data['message'] = "No data";
    die(json_encode($data));
} else {
    $user = new User();
    if ($user->login($_POST['email'], $_POST['token'])) {

        $device = $user->getDevice($_POST['device_id']);
        if ($device != NULL) {
            $GLOBALS['api']['user'] = $user;
            $GLOBALS['api']['device'] = $device;
        } else {
            $data['result'] = "error";
            $data['message'] = "Device not found";
            die(json_encode($data));
        }
    } else {

        $data['result'] = "error";
        $data['message'] = "Credential error";
        die(json_encode($data));
    }
}

/**
 *
 * @return User
 */
function getUser() {
    return $GLOBALS['api']['user'];
}

/**
 *
 * @return Device
 */
function getDevice() {
    return $GLOBALS['api']['device'];
}

?>
