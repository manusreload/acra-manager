<?php
require_once "../core/auth.php";
$core = new CDMCore();
$id = mysqli_real_escape_string($core->conn->mysql_conn, $_REQUEST['id']);
$res = mysqli_query($core->conn->mysql_conn, "SELECT * FROM reports WHERE REPORT_ID ='$id'");

if ($row = mysqli_fetch_array($res)) {
    foreach ($row as $key => $value) {
        if (!is_numeric($key) && !empty($value)) {
            ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo $key ?></h3>
                </div>
                <div>
                    <pre><?php echo $value ?></pre>
                </div>
            </div>
            <?php
        }
    }
}
?>
