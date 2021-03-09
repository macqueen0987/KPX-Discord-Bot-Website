<?php 
    include('../include/phpframe.php');
    include('../include/dbframe.php');

    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    $org_id = $_POST['org_userid'];
    $loginid = $_POST['userid'];
    $loginpw = $_POST['userpasswd'];
    $loginnick = $_POST['usernick'];
    $loginpwck = $_POST['userpasswd_check'];
    $org_passwd = $_POST['original_passwd'];
    $loginType = 0;

    if (strlen($loginid) < 1 && strlen($loginpw) < 1 && strlen($loginnick) < 1 && strlen($loginpw) < 1) {
        echo "<script>
        alert('변경할 정보가 없습니다.');
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
    
    echo "1";
    if (strlen($loginid) < 1) {
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
            alert('이미 등록된 아이디입니다.');
            history.go(-1);
            </script>";
            die();
        }
    }
    
    echo "2";
    $stmt = $mysqli->prepare("SELECT EXISTS(SELECT * FROM user_info WHERE user_id=? AND user_pw=PASSWORD( ? )) AS exist");
    $exist = false;
    if ($stmt) {
        $stmt->bind_param("ss", $org_id, $org_passwd);
        $stmt->execute();
        $stmt->bind_result($exist);
        $stmt->fetch();
        $stmt->close();
    }else{
        die('db error');
    }

    if (!$exist) {
        echo "<script>
        alert('비밀번호를 잘못 입력하셨습니다.');
        history.go(-1);
        </script>";
        die();
    }


    $stmt = $mysqli->prepare("SELECT user_nick FROM user_info WHERE user_id = ?");
    $org_nick = null;
    if($stmt){
        $stmt->bind_param("s", $org_id);
        $stmt->execute();
        $stmt->bind_result($org_nick);
        $stmt->fetch();
        $stmt->close();

    }
    else{
       die ("Mysql Error: " . $mysqli->error);
    }

    if (strlen($loginid) < 1) {
        $loginid = $org_id;
    }
    if (strlen($loginnick) < 1) {
        $loginnick = $org_nick;
    }
    if (strlen($loginpw) < 1) {
        $loginpw = $org_passwd;
    }

    $query = 'UPDATE user_info SET user_id = ?, user_nick = ?, user_pw = password(?) WHERE user_id = ?';
    $stmt = $mysqli->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ssss", $loginid, $loginnick, $loginpw, $org_id);
        $stmt->execute();
        $stmt->close();
    }
    else{
        die($mysqli->error);
    }

    echo "<script>alert('성공적으로 변경되었습니다!');location.href = '../index.php';</script>";

?>