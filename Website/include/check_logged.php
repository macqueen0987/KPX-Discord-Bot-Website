<?php
    if (!isset($_SESSION['LoginUserId'])) {
        echo "<script> alert('로그인을 해주세요!'); location.href = './login.php';</script>";
        die();
    }
?>