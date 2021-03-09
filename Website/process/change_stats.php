<?php

    include('../include/phpframe.php');
    include('../include/check_logged.php');
    include('../include/dbframe.php');


    if (isset($_POST['type'])) {
        $type = $_POST['type'];

        $stmt = $mysqli->prepare("SELECT ".$type." FROM WOP_buyback_stats");
        $stats = null;
        if ($stmt) {
            $stmt->execute();
            $stmt->bind_result($stats);
            $stmt->fetch();
            $stmt->close();
        }else{
            die('db error');
        }


        $stats = 1 - intval($stats);
        $query = "UPDATE WOP_buyback_stats SET ".$type." = ?";
        $stmt = $mysqli->prepare($query);
        if ($stmt) {
            $stmt->bind_param('d', $stats);
            $stmt->execute();
            $stmt->close();
        }else{
            die($mysqli->error);
        }
        echo $stats;
        exit;
    }


?>