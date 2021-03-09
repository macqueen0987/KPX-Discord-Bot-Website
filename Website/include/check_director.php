<?php
    if (!isset($_SESSION['LoginUserType'])) {
        echo "<script> alert('로그인을 해주세요!'); location.href = './login.php';</script>";
        die();
    }

    if ($_SESSION['LoginUserType'] != 1) {
        echo "<script> alert('디렉진 전용 페이지 입니다!'); location.href = './index.php';</script>";
        die();
    }
?>