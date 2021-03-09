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
    <?php include('./include/header.php'); ?>
    <script type="text/javascript" src="./js/register.js"></script>
    <meta property="og:url" content="http://kpx.kro.kr/register.php">
    <meta property="og:title" content="K-PAX Corp Sign Up">
</head>
<body>
    <?php include('./include/navbar.php') ?>
    <section class ="body">
        <div class="register-box">
            <form class="form-register" action="./process/register.php" method="post">
                <h1>환영합니다!</h1>
                <input type="text" name="userid" id="inputId" class="form-control" placeholder="사용자 계정" required autofocus>
                <input type="text" name="usernick" id="inputNick" class="form-control" placeholder="인게임 이름 (다계정일 경우 메인계정)" required>
                <input type="password" name="userpasswd" id="inputPassword" class="form-control" placeholder="암호" required>
                <input type="password" name="userpasswd_check" id="inputPassword_check" class="form-control" placeholder="암호 확인" required>
                <div style="position: relative; left: 310px; top: -39px; width: 30px;">
                    <label class="switch" title="비밀번호 보이기">
                      <input type="checkbox" onclick="show_pass()" title="비밀번호 보이기">
                      <span class="slider round" title="비밀번호 보이기"></span>
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit" style="margin-top: 20px;">가입하기</button>
                <p><a href="login.php">로그인하기</a></p>
            </form>
        </div>
    </section>
</body>
</html>