<?php 
    require_once'../include/phpframe.php';
    require_once'../include/dbframe.php';
    
    $loginid = $_POST['userid'];
    $loginpw = $_POST['userpasswd'];
    $last_page = $_POST['last_page'];

    $now = date("Y-m-d H:i:s");

    $stmt = $mysqli->prepare("SELECT activated_datetime FROM user_info WHERE user_id = ?");

    $activated = null;
    if($stmt){
        $stmt->bind_param("s",$loginid);
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
            $stmt->bind_param("s", $loginid);
            $stmt->execute();
            $stmt->close();
        }
        else{
            die('db error');
        }
    }


    $stmt = $mysqli->prepare("SELECT user_id, user_type, user_nick, activated FROM user_info WHERE user_id=? AND user_pw=PASSWORD( ? )");

    $user_id = $user_type = $user_nick = $activated = null;

    if($stmt){
        $stmt->bind_param("ss",$loginid,$loginpw);
        $stmt->execute();
        $stmt->bind_result($user_id,$user_type,$user_nick, $activated);
        $stmt->fetch();
        $stmt->close();
    }
    else{
        die("db error");
    }

    if($user_id == null){
        echo "<script>
        alert('사용자계정 또는 비밀번호가 잘못 입력되었습니다.');
        history.go(-1);
        </script>";
        die();
    }

    if (!$activated) {
        echo "<script>
        alert('아직 활성화 되지 않은 계정입니다. 모든 계정은 일정 주기마다 비활성화 됩니다. 디코 봇으로 먼저 활성화 해주세요! KPX 콥원이라면 KPX 디코방 놀이터 채널에, 다른 한국인이시라면 KATO 디코방 놀이터 채널에 !활성화 [아이디]   If you are not a member of GENK Member... Sorry This site is for GENK Corps!');
        history.go(-1);
        </script>";
        die();
    }

    $_SESSION['LoginUserId'] = $user_id;
    $_SESSION['LoginUserType'] = $user_type;
    $_SESSION['LoginUserNick'] = $user_nick;

    if (strlen($last_page) > 1) {
        echo "<script>location.href='../index.php';</script>";
    }else{
        echo "<script>history.go(-1);</script>";
    }
        
?>