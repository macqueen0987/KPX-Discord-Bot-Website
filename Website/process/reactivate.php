<?php
    require_once'../include/phpframe.php';
    require_once'../include/dbframe.php';

    $now = date("Y-m-d H:i:s");


    $user_id = $_POST['user_id'];
    if (!isset($user_id)) {
        die();
    }

    $stmt = $mysqli->prepare("SELECT activated_datetime FROM user_info WHERE user_id = ?");

    $activated = null;

    if($stmt){
        $stmt->bind_param("s",$user_id);
        $stmt->execute();
        $stmt->bind_result($activated);
        $stmt->fetch();
        $stmt->close();
    }
    else{
        die("db error");
    }

    $activated_datetime = strtotime($activated);
    $diff=strtotime($now)-$activated_datetime;
    if ($diff > 259200) {
        $stmt = $mysqli->prepare("UPDATE user_info SET user_type = 0, activated = false WHERE user_id = ?");

        if ($stmt) {
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $stmt->close();
        }
        else{
            die('db error');
        }
    }

?>