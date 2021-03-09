<?php 
    include('../include/phpframe.php');
    include('../include/dbframe.php');

    error_reporting(E_ALL);
    ini_set("display_errors", 1);


    $loginid = $_POST['userid'];
    $loginpw = $_POST['userpasswd'];
    $loginnick = $_POST['usernick'];
    $loginpwck = $_POST['userpasswd_check'];
    $loginType = 0;
    
    // $loginid = "sds";
    // $loginpw = "123";
    // $loginnick = "gul";
    // $loginpwck = "123";
    // $loginEmail = "1234";
    // $loginType = 0;
    $stmt = $mysqli->prepare("SELECT user_id, user_nick FROM user_info WHERE user_id=?");
    
    $user_id = null;
    if($stmt){
        $stmt->bind_param("s",$loginid);
        $stmt->execute();
        $stmt->bind_result($user_id,$user_nick);
        $stmt->fetch();
        $stmt->close();
    }
    else{
        die("db error");
    }
    
    if($user_id != null){
        echo "<script>
        alert('이미 등록된 유저입니다.');
        history.go(-1);
        </script>";
        die();
    }
    
    if(strcmp($loginpw,$loginpwck)!=0){
        echo "<script>
        alert('비밀번호가 비밀번호 확인과 일치하지 않습니다. 다시 확인해주세요.');
        history.go(-1);
        </script>";
        die();   
    }

    else{
        $stmt = $mysqli->prepare("INSERT INTO user_info(user_id, user_type, user_nick, user_pw) VALUES('$loginid', '$loginType', '$loginnick', PASSWORD('$loginpw'))");
        if($stmt){
            $stmt->execute();
            $stmt->fetch();
            $stmt->close();

        }
        else{
           die ("Mysql Error: " . $mysqli->error);
        }

    }

    echo "<script>alert('성공적으로 가입되었습니다!');location.href = '../index.php';</script>";

?>