<?php
$serverName = "172.26.18.9";
$databaseName = "IQ_ASDKV8";
$myusername = "PortalBancolombia";
$mypassword = '6$KLpUg@3061v&';

$connectionInfo = array("Database" => $databaseName, "UID" => $myusername, "PWD" => $mypassword);
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(json_encode(array("error" => sqlsrv_errors())));
}
?>