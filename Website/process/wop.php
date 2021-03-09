<?php
    require_once'../include/phpframe.php';
    require_once'../include/dbframe.php';
    
    $user_id = $_SESSION['LoginUserId'];
    $wop = $_POST['WOP'];
    $ingame_id = $_POST['ingame_id'];



    if(!empty($_FILES["image"]["name"])) { 
        // Get file info 
        $fileName = basename($_FILES["image"]["name"]); 
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
         
        // Allow certain file formats 
        $allowTypes = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType, $allowTypes)){ 
            $image = $_FILES['image']['tmp_name']; 
            $imgContent = addslashes(file_get_contents($image)); 

        }else{ 
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.'; 
        } 
    }else{ 
        $statusMsg = 'Please select an image file to upload.'; 
    } 

    // echo "1<br>";
    $stmt = $mysqli->prepare('SELECT discord_id FROM user_info WHERE user_id = ?');
    $discord_id = 0;
    if ($stmt) {
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        $stmt->bind_result($discord_id);
        $stmt->fetch();
        $stmt->close();
    }else{
        die('db error');
    }
    // echo $discord_id."<br>";
    if (!empty($_FILES["image"]["name"])) {
        $query = 'INSERT INTO WOP(discord_id, web_id, product, kill_log) VALUES(?, ?, ?,"'.$imgContent.'")';
        if (strlen($ingame_id) > 0) {
            $query = 'INSERT INTO WOP(discord_id, web_id, ingame_id, product, kill_log) VALUES(?, ?, ?, ?,"'.$imgContent.'")';
        }
    }else{
        $query = 'INSERT INTO WOP(discord_id, web_id, product) VALUES(?, ?, ?)';
        if (strlen($ingame_id) > 0) {
            $query = 'INSERT INTO WOP(discord_id, web_id, ingame_id, product) VALUES(?, ?, ?, ?)';
        }
    }
    // echo "2<br>";
    // echo "$query<br>";
    $stmt = $mysqli->prepare($query);
    if ($stmt) {
        if (strlen($ingame_id) > 0) {
            $stmt->bind_param('isss', $discord_id, $user_id, $ingame_id, $wop);
        }else{
            $stmt->bind_param('iss', $discord_id, $user_id, $wop);
        }
        $stmt->execute();
        $stmt->close();
    }else{
        die($mysqli->error);
    }

   echo "<script>alert('성공적으로 신청되었습니다!');location.href = '../index.php'</script>";

?>