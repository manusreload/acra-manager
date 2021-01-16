<?php
error_log("OK");
require( "class/CDMCore.php" );
$core = new CDMCore();
$app = $core->getAppDetails($_REQUEST['app']);

error_log(print_r($_POST, true));

if($_POST['PACKAGE_NAME'] == $app->pkg)
{
    $out = "";
    $keys="";
    $values="";
    foreach($_POST as $key => $value)
    {
            $keys .= ($keys=='')?'':' , ';
            $keys .= $key;

            $values .= ($values=='')?'':' , ';
            $values .= "'" . htmlentities($value,ENT_QUOTES ) . "'";
    }
    $hash = $core->get_error_hash($_POST['STACK_TRACE']);
    $sql = "INSERT INTO reports (app, " . $keys . ", STACK_TRACE_HASH) VALUES ('{$app->appId}', " . $values . ", '$hash')";

    $res = mysqli_query($core->conn->mysql_conn, $sql);

    if(!$res)
    {
        $out = mysqli_error($core->conn->mysql_conn);
        file_put_contents("test.txt", "=================\n" . $sql . "\n\n" . $out . "\n", FILE_APPEND);
    }
    if($app->notify)
    {
        $message = "<h2>La aplicacion " . $app->name . " [" . $app->pkg . "] ha crasheado</h2>";
        $message .= "<h3>Dispositivo</h3>";
        $message .= "Modelo: " . $_POST['BRAND'] . " " . $_POST['PHONE_MODEL'] . "<br>";
        $message .= "Version de Android: " . $_POST['ANDROID_VERSION'] . "<br>";
        $message .= "<p><b>Version:</b> " . $_POST['APP_VERSION_NAME'] . "<br>";
        $message .= "<b>Codigo:</b> " . $_POST['APP_VERSION_CODE'] . "</p>";
        $message .= "<h3>Stack Trace</h3>";
        $message .= "<pre>" . colourStracTrace($_POST['STACK_TRACE'], $app->error_pkg) . "</pre>";
        $message .= $out;
        $core->send_mail($app->author, $app->name . " a crasheado", $message, $app->name . " @ Crash-Reporting");
    }
}



function colourStracTrace($data, $pkg)
{
	$ret = "";
	$data = explode("\n",$data);
	foreach($data as $line)
	{
		if(strpos($line,$pkg) > -1)
		{
			$ret .= '<font color="red">'.$line.'</font><br />';
		}
		else
		{
			$ret .= $line.'<br />';
		}
	}
	return $ret;
}

