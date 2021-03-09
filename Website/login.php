<?php 
    include('./include/phpframe.php');

    if(isset($_SESSION['LoginUserId'])){
        echo "<script>
        alert('이미 로그인되어 있습니다');
        location.href = 'index.php'
        </script>";
        die();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="./js/login.js"></script>
    <meta property="og:url" content="http://kpx.kro.kr/login.php">
    <meta property="og:title" content="K-PAX Corp Login">
    <?php include('./include/header.php'); ?>
</head>
<body>
    <?php include('./include/navbar.php') ?>
    <section class ="body">
        <div class="login-box">
            <form class="form-signin" action="./process/login.php" method="post">
                <h1>환영합니다!</h1>
                <input type="text" name="userid" id="inputId" class="form-control" placeholder="사용자 계정" required autofocus>
                <input type="password" name="userpasswd" id="inputPassword" class="form-control" placeholder="암호" required>
                <input type="hidden" name="last_page" value="login">
                <div style="position: relative; left: 310px; top: -39px; width: 30px;">
                    <label class="switch" title="비밀번호 보이기">
                      <input type="checkbox" onclick="show_pass()" title="비밀번호 보이기">
                      <span class="slider round" title="비밀번호 보이기"></span>
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit" style="margin-top: 20px;">로그인</button>
                <p>비밀번호를 분실한 경우 macqueen0987에게 DM!</p>
                <!-- <p><a href="register.php">가입하기</a></p> -->
            </form>
        </div>
    </section>
</body>
</html>