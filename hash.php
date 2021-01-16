<?php
/**
 * File: ${NAME}
 * Created by: mmunoz
 * At: 16/1/21
 * In project: ACRA-Manager / hash.php
 */

require_once "class/CDMCore.php";

$core = new CDMCore();
$reports = $core->conn->query("SELECT STACK_TRACE, REPORT_ID FROM reports");
while ($row = mysqli_fetch_array($reports)) {
    $hash = $core->get_error_hash($row['STACK_TRACE']);
    if($hash == '') {
        echo "Error: " . $row['REPORT_ID'] . "\n";
        continue;
    }
    $core->conn->query("UPDATE reports SET STACK_TRACE_HASH = '$hash' WHERE REPORT_ID = '{$row['REPORT_ID']}'");
}

var_dump($reports);
