<?php
require_once "../core/auth.php";
$id = mysql_real_escape_string($_REQUEST['id']);
$res = mysql_query("SELECT * FROM reports WHERE REPORT_ID ='$id'");

if ($row = mysql_fetch_array($res)) {
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
