<?php
$db_host = "localhost";
$db_user = "EVE";
$db_passwd = "password";
$db_name = "EVE";
$db_port = "18001";

$mysqli = new mysqli($db_host, $db_user, $db_passwd, $db_name, $db_port);

if(mysqli_connect_error()){
    die("db error");
}
?>