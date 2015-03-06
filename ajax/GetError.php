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

if (haveLogin()) {
    if (isset($_GET['id'])) {
        $app = getUser()->getApp($_GET['id']);
        
        $package = mysql_real_escape_string($app->pkg);
        $error_pkg = mysql_real_escape_string($app->error_pkg);
        $vercode = mysql_real_escape_string($_REQUEST['vercode']);
        $res = mysql_query("SELECT count(STACK_TRACE) FROM reports WHERE PACKAGE_NAME ='$package' AND  APP_VERSION_CODE = $vercode AND RESOLVED = 0 GROUP BY STACK_TRACE");
        $count = mysql_num_rows($res);
        $fields = 10;
        $pages = (int) ($count / $fields);
        if ($pages * $fields < $count)
            $pages++;
        $page = 0;
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
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
        	$res = mysql_query("
                    SELECT STACK_TRACE,count(STACK_TRACE)   FROM ( SELECT * FROM reports WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode  ORDER BY USER_CRASH_DATE DESC) t GROUP BY t.STACK_TRACE ORDER BY t.USER_CRASH_DATE DESC LIMIT $q, 10
                        ");
		if(!$res) die(mysql_error());
        }
        else
        {
        	$res = mysql_query("SELECT STACK_TRACE,count(STACK_TRACE) FROM reports WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode AND RESOLVED = 0 GROUP BY STACK_TRACE ORDER BY $order LIMIT $q, 10");
        }

        echo '<table class="table"><tr>';
        echo '<th><a href="?app='.$_REQUEST['id'] . '&order=&vercode=' . $vercode. '&page=0">Count</a></th>';
        echo '<th><a href="?app='.$_REQUEST['id'] . '&order=date&vercode=' . $vercode. '&page=0">Date</a></th>';
        echo '<th>StackTrace</th><th>Devices</th></tr>';
        while ($row = mysql_fetch_array($res)) {
            $res2 = mysql_query("SELECT * FROM reports WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode AND STACK_TRACE = '" . $row['STACK_TRACE'] . "'");
            $t = 0;
            $lastTime = 0;
			$info = array();
            while ($row2 = mysql_fetch_array($res2)) {
                $info['PHONE_MODELS'][$row2['PHONE_MODEL']]++;
                $info['USER_EMAILS'][$row2['USER_EMAIL']]++;
                $t++;
				if(strtotime($row2['USER_CRASH_DATE']) > $lastTime)
				{
					$lastTime = strtotime($row2['USER_CRASH_DATE']) ;
				}
            }
            echo "<tr>";
            echo "<td><span class=\"badge\">" . $t . "</span></td>";
            echo "<td style='width: 100px'>" . date("d-m-y H:i", $lastTime) . "</td>";
            echo "<td><pre>";
            $trace = explode("\n",$row['STACK_TRACE']);
            
            echo colourStracTrace($trace[0], $error_pkg);
            echo "</pre><br><a href=\"details.php?app={$_REQUEST['id']}&vercode={$vercode}&id=$q&order={$_REQUEST['order']}\">Detalles &RightArrow; </a> ";
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
