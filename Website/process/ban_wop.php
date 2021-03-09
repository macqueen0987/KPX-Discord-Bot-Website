<?php
    include('../include/phpframe.php');
    include('../include/check_logged.php');
    include('../include/dbframe.php');

    $banned = array_keys($_POST);
    $banned = json_encode($banned); 
    // var_dump($banned);
    $stmt = $mysqli->prepare("UPDATE WOP_price SET banned = ?");
    if ($stmt) {
        $stmt->bind_param('s', $banned);
        $stmt->execute();
        $stmt->close();
    }else{
        die($mysqli->error);
    }

    echo "<script>alert('성공적으로 처리되었습니다.'); history.go(-1);</script>";
?>