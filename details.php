<?php
require_once "core/auth.php";
require_once "core/check-user.php";

error_reporting(E_ALL);

function colourStracTrace($data, $pkg) {
    $ret = "";
    $data = split("\n", $data);
    foreach ($data as $line) {
        if (strpos($line, $pkg) > -1) {
            $ret .= '<font color="red">' . $line . '</font><br />';
        } else {
            $ret .= $line . '<br />';
        }
    }
    return $ret;
}

if (!haveLogin()) {
    header("Location: login.php");
} else {
    if (isset($_REQUEST['app']) && isset($_REQUEST['vercode']) && isset($_REQUEST['id'])) {
        $app = getUser()->getApp($_REQUEST['app']);
        $q = mysql_real_escape_string($_REQUEST['id']);
        $package = mysql_real_escape_string($app->pkg);
        $error_pkg = mysql_real_escape_string($app->error_pkg);
        $vercode = mysql_real_escape_string($_REQUEST['vercode']);
        
        $order = "COUNT(STACK_TRACE) DESC";
        if (isset($_GET['order'])) {
            if ($_REQUEST['order'] == "date") {
                $order = "USER_CRASH_DATE DESC";
            }
        }


        $q = mysql_real_escape_string($_GET['id']);
        if (isset($_GET['order']) && $_GET['order'] == 'date') {
            $res = mysql_query("SELECT *  FROM ( SELECT * FROM reports WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode  ORDER BY USER_CRASH_DATE DESC) t GROUP BY t.STACK_TRACE ORDER BY t.USER_CRASH_DATE DESC LIMIT $q, 1
                       ");
            if (!$res)
                die(mysql_error());
        }
        else {
            $res = mysql_query("SELECT * FROM reports WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode AND RESOLVED = 0 GROUP BY STACK_TRACE ORDER BY COUNT(STACK_TRACE) DESC LIMIT $q, 1");
            
            if (!$res)
                die(mysql_error());
        }
        //$res = mysql_query("SELECT * FROM reports WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode AND RESOLVED = 0 GROUP BY STACK_TRACE ORDER BY $order DESC LIMIT $q, 1");
        if ($row = mysql_fetch_array($res)) {
            if(isset($_REQUEST['resolve']))
            {
                $update = mysql_query("UPDATE reports SET `RESOLVED` = '1' WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode AND STACK_TRACE = '" . $row['STACK_TRACE'] . "'");
                if(!$update) echo mysql_error ();
            }
            $res2 = mysql_query("SELECT * FROM reports WHERE PACKAGE_NAME ='$package' AND APP_VERSION_CODE = $vercode AND STACK_TRACE = '" . $row['STACK_TRACE'] . "' ORDER BY USER_CRASH_DATE DESC");
            $t = 0;
            $first = NULL;
            while ($row2 = mysql_fetch_array($res2)) {
                if ($t == 0) {
                    $first = $row2;
                }
                $info['PHONE_MODELS'][$row2['PHONE_MODEL']] ++;
                $info['USER_EMAILS'][$row2['USER_EMAIL']] ++;
                $info['VERSIONS'][$row2['ANDROID_VERSION']] ++;
                $info['TIME'][date("y-m-d", strtotime($row2['USER_CRASH_DATE']))] ++;
                $info['TIME_OUT'][date("d/m/y", strtotime($row2['USER_CRASH_DATE']))] ++;
                $info['COMMENTS'][] = array("comment" => $row2['USER_COMMENT'], "device" => $row2['BRAND'] . " " . $row2['PHONE_MODEL']);
                $info['DETAILS'][] = array("name" => $row2['PHONE_MODEL'] , "id" => $row2['REPORT_ID']);
                if($row2['RESOLVED'] == "1")
                {
                    $info['RESOLVED'] = true;
                }
                else
                {
                    $info['RESOLVED'] = false;
                }
                $t++;
            }
        } else {
            die("ID not found!");
        }
        ?>
        <!DOCTYPE html>
        <html lang = "es">
            <head>
        <?php require_once "fragment/head.php";
        ?>
                <script src="http://code.jquery.com/jquery-latest.js"></script>
                <script>
                    function loadapp(id, vercode) {
                        ajaxLoad({
                            content: "app_error_container",
                            error: "ajax-error",
                            loading: "ajax-loading",
                            url: "ajax/GetError.php?id=" + id + "&vercode=" + vercode + "&page=<?php echo $_REQUEST['page']; ?>"
                        });
                    }
                    function load_details(id) {
                        ajaxLoad({
                            content: "app_details_container",
                            error: "ajax-error",
                            loading: "ajax-loading",
                            url: "ajax/GetDetails.php?id=" + id
                        });
                    }
                </script>
                
                <style type="text/css">
                @media (min-width: 768px) { 
                    .sb-fixed{
                      position: fixed;
                      float: right;
                    } 
                  }
                </style>
        <?php
        $time = time();
        foreach ($info['TIME'] as $key => $value) {
            if (strtotime($key) < $time) {
                $time = strtotime($key);
            }
        }

        while ($time < time()) {
            if ($time + 60 * 60 * 24 > time()) {
                $time = time();
            } else {
                $time += 60 * 60 * 24;
            }
            $info['TIME_OUT'][date("d/m/y", $time)] = 0;
        }
        ?>
                <script type="text/javascript" src="https://www.google.com/jsapi"></script>
                <script type="text/javascript">
                    google.load("visualization", "1", {packages: ["corechart"]});
                    google.setOnLoadCallback(drawChart);
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Dispositivo', 'Crashes']
                <?php
                foreach ($info['PHONE_MODELS'] as $key => $value) {
                    echo ",['" . $key . "'," . $value . "]";
                }
                ?>

                        ]);
                        var options = {
                            title: 'Dispositivos',
                            is3D: true,
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('devices'));
                        chart.draw(data, options);

                        var data = google.visualization.arrayToDataTable([
                                ['Version', 'Crashes']
        <?php
        foreach ($info['VERSIONS'] as $key => $value) {
            echo ",['" . $key . "'," . $value . "]";
        }
        ?>

                        ]);
                                var options = {
                            title: 'Versiones de Android',
                            is3D: true,
                            pieSliceText: 'label',
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('android'));
                        chart.draw(data, options);

                        var data = google.visualization.arrayToDataTable([
                                ['Date', 'Crashes']
        <?php
        foreach ($info['TIME_OUT'] as $key => $value) {
            echo ",['" . $key . "'," . $value . "]";
        }
        ?>
                        ]);
                                var options = {
                            title: 'Crash Day',
                            vAxis: {minValue: 0}
                        };

                        var chart = new google.visualization.AreaChart(document.getElementById('time_trace'));
                        chart.draw(data, options);
                    }
                </script>
            </head>

            <body>
        <?php require_once "fragment/menu.php"; ?>
                <div class="container">
                    <section id="info">
                        <div class="page-header">
                            <h2>Detalles del error<small> - <a href="errors.php?app=<?php echo $_REQUEST['app']; ?>&vercode=<?php echo $_REQUEST['vercode']; ?>">Atras</a></small></h2>
                        </div>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Veces</h3>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h2><?php echo $t; ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Dispositivos</h3>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h2><?php echo count($info['PHONE_MODELS']); ?></h2>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-lg-2">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Android</h3>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h2><?php echo count($info['VERSIONS']); ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Ultimo Crash</h3>
                                    </div>
                                    <div class="panel-body text-center">
        <?php echo $first['USER_CRASH_DATE']; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2">
                            </div>
                            <div class="col-lg-2">
                                <?php
                                if( $info['RESOLVED'])
                                {
                                    ?>
                                <a href="details.php?<?php echo $_SERVER['QUERY_STRING']; ?>&resolve=1" class="center btn btn-large btn-info">Resuelto!</a>
                            
                                <?php
                                
                                }
                                else
                                {
                                    ?>
                                <a href="details.php?<?php echo $_SERVER['QUERY_STRING']; ?>&resolve=1" class="center btn btn-large btn-success">Resolver</a>
                            
                                <?php
                                }
                                ?>
                                </div>

                        </div>
                        <div class="row">

                            <div class="col-lg-6">
                                <div id="devices" style="height:400px;"></div>
                            </div>
                            <div class="col-lg-6">
                                <div id="android" style="height:400px;"></div>
                            </div>
                        </div>
                        <div id="time_trace">

                        </div>
                        <div class="row">
                            
                            <div class="col-lg-9">
                                
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Stack Trace</h3>
                                    </div>
                                    <div class="panel-body">
                                        <pre><?php echo colourStracTrace($row['STACK_TRACE'], $error_pkg); ?></pre>
                                    </div>

                                </div>

                                <div id="app_details_container">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Users Comments</h3>
                                    </div>
                                    <div>
                                        <?php
                                                foreach ($info['COMMENTS'] as $value)
                                                {
                                                    if($value['comment'] != "")
                                                    {
                                                        
                                                        echo "<h4>An user whit an " . $value['device'] . "</h4>";
                                                        echo "<pre>" . $value['comment'] . "</pre>";
                                                    }
                                                }
                                        ?>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Detailed Crash</h3>
                                    </div>
                                    <div>
                                        <?php
                                                foreach ($info['DETAILS'] as $value)
                                                {
                                                    echo "<a href='#' onclick='load_details(\"" . $value['id'] . "\")'>". $value['name'] . "</a><br />";
                                                }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>

        <?php require_once "fragment/footer.php"; ?>

                </div>

        <?php require_once "fragment/scripts.php"; ?>

            </body>
        </html>
        <?php
    } else {
        header("Location: errors.php");
    }
}
?>

