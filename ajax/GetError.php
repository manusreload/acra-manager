<?php
require_once "../core/auth.php";

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
$core = new CDMCore();
if (haveLogin()) {
    if (isset($_GET['id'])) {
        $app = getUser()->getApp($_GET['id']);

        $package = mysqli_real_escape_string($core->conn->mysql_conn, $app->pkg);
        $error_pkg = mysqli_real_escape_string($core->conn->mysql_conn, $app->error_pkg);
        $vercode = mysqli_real_escape_string($core->conn->mysql_conn, $_REQUEST['vercode']);
        $res = mysqli_query($core->conn->mysql_conn, "SELECT count(STACK_TRACE) FROM reports WHERE PACKAGE_NAME ='$package' AND  APP_VERSION_CODE = $vercode AND RESOLVED = 0 GROUP BY STACK_TRACE");
        $count = mysqli_num_rows($res);
        $fields = 10;
        $pages = (int) ($count / $fields);
        if ($pages * $fields < $count)
            $pages++;
        $page = 0;
        if (isset($_GET['page'])) {
            $page = intval($_GET['page']);
        }

		$order = "COUNT(STACK_TRACE) DESC";
        if(isset($_GET['order']))
        {
        	if($_REQUEST['order'] == "date")
        	{
				$order = "USER_CRASH_DATE DESC";
        	}
        }
        $paginator = "";
        $paginator = '<ul class="pagination">';
        for ($i = 0; $i < $pages; $i++) {
            if($i == $page)
            {
                $paginator .= '<li class="active">';
            }
            else
            {
                $paginator .=  '<li>';
            }
            $paginator .= "<a href=\"?app={$_REQUEST['id']}&order={$_REQUEST['order']}&vercode=$vercode&page=$i\">" . ($i + 1) . "</a> ";
            $paginator .= '</li>';
        }
        $paginator .=  '</ul>';
        echo $paginator;
        $q = $page * $fields;

        if(isset($_GET['order']) && $_GET['order'] == 'date')
        {
        	$res = mysqli_query($core->conn->mysql_conn, "
                    SELECT STACK_TRACE,count(STACK_TRACE)   FROM ( SELECT * FROM reports WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode  ORDER BY USER_CRASH_DATE DESC) t GROUP BY t.STACK_TRACE ORDER BY t.USER_CRASH_DATE DESC LIMIT $q, 10
                        ");
		if(!$res) die(mysqli_error($core->conn->mysql_conn));
        }
        else
        {
        	$res = mysqli_query($core->conn->mysql_conn, "SELECT STACK_TRACE_HASH,count(STACK_TRACE_HASH) as count FROM reports WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode AND RESOLVED = 0 GROUP BY STACK_TRACE_HASH ORDER BY $order LIMIT $q, 10");
        }

        echo '<table class="table"><tr>';
        echo '<th><a href="?app='.$_REQUEST['id'] . '&order=&vercode=' . $vercode. '&page=0">Count</a></th>';
        echo '<th><a href="?app='.$_REQUEST['id'] . '&order=date&vercode=' . $vercode. '&page=0">Date</a></th>';
        echo '<th>StackTrace</th><th>Devices</th><th></th></tr>';
        while ($row = mysqli_fetch_array($res)) {
            $res2 = mysqli_query($core->conn->mysql_conn, "SELECT * FROM reports WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode AND STACK_TRACE_HASH = '" . $row['STACK_TRACE_HASH'] . "'");
            $t = 0;
            $lastTime = 0;
			$info = array(
			    'PHONE_MODELS' => [],
			    'USER_EMAILS' => [],
            );
			$stack = null;
            while ($row2 = mysqli_fetch_array($res2)) {
                if(!isset($info['PHONE_MODELS'][$row2['PHONE_MODEL']])) {
                    $info['PHONE_MODELS'][$row2['PHONE_MODEL']] = 0;
                }
                if(!isset($info['PHONE_MODELS'][$row2['USER_EMAIL']])) {
                    $info['PHONE_MODELS'][$row2['USER_EMAIL']] = 0;
                }
                $info['PHONE_MODELS'][$row2['PHONE_MODEL']]++;
                if(isset($info['USER_EMAILS'][$row2['USER_EMAIL']])) {
                    $info['USER_EMAILS'][$row2['USER_EMAIL']]++;
                }
                $t++;
				if(strtotime($row2['USER_CRASH_DATE']) > $lastTime)
				{
					$lastTime = strtotime($row2['USER_CRASH_DATE']) ;
				}

                $stack = $row2['STACK_TRACE'];
            }
            echo "<tr>";
            echo "<td><span class=\"badge\">" . $t . "</span></td>";
            echo "<td style='width: 100px'>" . date("d-m-y H:i", $lastTime) . "</td>";
            echo "<td><pre>";
            $trace = explode("\n",$stack);

            echo colourStracTrace($trace[0], $error_pkg);
            echo "</pre><br><a href=\"details.php?app={$_REQUEST['id']}&vercode={$vercode}&id={$row['STACK_TRACE_HASH']}&order={$_REQUEST['order']}\">Detalles &RightArrow; </a> ";
            echo "</td>";
            echo "<td>";
            foreach ($info['PHONE_MODELS'] as $key => $value) {
                echo $key . ": " . $value . "<br>";
            }
            echo "<hr>";
            foreach ($info['USER_EMAILS'] as $key => $value) {
                echo $key . ": " . $value . " <br>";
            }
            echo "</td>";
            echo "<td><input type='checkbox' name='reports[]' value='{$row['STACK_TRACE_HASH']}'></td>";
            echo "</tr>";
            $q++;
        }
        echo '</table>';

        echo $paginator;
        ?>

        <?php
    } else {
        echo "No se ha encontrado el id!";
    }
} else {
    echo "Por favor, logeese de nuevo <a href='login.php'>Login</a>";
}
?>
