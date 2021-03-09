<?php 
    include('./include/phpframe.php');
    include('./include/check_logged.php');
?>

<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="./js/user_info.js"></script>
    <?php include('./include/header.php'); ?>
</head>
<body>
    <?php include('./include/navbar.php') ?>
    <section class ="body">
        <div class="register-box">
            <form class="form-register" action="./process/user_info.php" method="post">
                <h1>사용자 정보변경</h1>
                <input type="hidden" name="org_userid" value=<?php echo '"'.$_SESSION['LoginUserId'].'"'; ?>>
                <input type="text" name="userid" id="inputId" class="form-control" placeholder="사용자 계정" autofocus>
                <input type="text" name="usernick" id="inputNick" class="form-control" placeholder="인게임 이름 (다계정일 경우 메인계정)">
                <input type="password" name="userpasswd" id="inputPassword" class="form-control" placeholder="새로운 암호">
                <input type="password" name="userpasswd_check" id="inputPassword_check" class="form-control" placeholder="암호 확인">
                <div style="position: relative; left: 310px; top: -39px; width: 30px;">
                    <label class="switch" title="비밀번호 보이기">
                      <input type="checkbox" onclick="show_new_pass()" title="비밀번호 보이기">
                      <span class="slider round" title="비밀번호 보이기"></span>
                    </label>
                </div>
                <input type="password" name="original_passwd" id="original_passwd" class="form-control" placeholder="기존 암호" required>
                <div style="position: relative; left: 310px; top: -39px; width: 30px;">
                    <label class="switch" title="비밀번호 보이기">
                      <input type="checkbox" onclick="show_old_pass()" title="비밀번호 보이기">
                      <span class="slider round" title="비밀번호 보이기"></span>
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">변경하기</button>
            </form>
        </div>
    </section>
</body>
</html>