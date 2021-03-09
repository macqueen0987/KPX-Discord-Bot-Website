<?php
    require_once'../include/phpframe.php';
    require_once'../include/dbframe.php';


    $user_id = $_SESSION['LoginUserId'];

    $arr = $_POST;
    $materials = array_keys($arr);

    $buyback_arr = array();
    $ore_arr = array();
    $mineral_arr = array();
    $pi_arr = array();
    // var_dump($_POST);   
    $ingame_id = $_POST['ingame_id'];
    // $emtpy = true;
    $ore_empty = $mineral_empty = $pi_empty = true;
    $ore_sum = $mineral_sum = $pi_sum = 0;
    for ($i=0; $i < count($arr)-5; $i++) { 
        if (intval($arr[$materials[$i]]) != 0) {
            $emtpy = false;
            if (strpos($materials[$i], 'ore_') !== false) {
                $ore_empty = false;
                $ore_arr = $ore_arr + array(str_replace('ore_', '', $materials[$i]) => intval($arr[$materials[$i]]));
            }
            if (strpos($materials[$i], 'mineral_') !== false) {
                $mineral_empty = false;
                $mineral_arr = $mineral_arr + array(str_replace('mineral_', '', $materials[$i]) => intval($arr[$materials[$i]]));
            }
            if (strpos($materials[$i], 'pi_') !== false) {
                $pi_empty = false;
                $pi_arr = $pi_arr + array(str_replace('pi_', '', $materials[$i]) => intval($arr[$materials[$i]]));
            }            
        }
    }

    if ($ore_empty && $mineral_empty && $pi_empty) {
        echo "<script>alert('값이 모두 비어있습니다!'); history.go(-1);</script>";
        die();
    }


    if (!$ore_empty) {
        $ore_arr = $ore_arr + array('ore_total' => $_POST['ore_total']);
    }
    if (!$mineral_empty) {
        $mineral_arr = $mineral_arr + array('mineral_total' => $_POST['mineral_total']);
    }
    if (!$pi_empty) {
        $pi_arr = $pi_arr + array('pi_total' => $_POST['pi_total']);
    }

    $buyback_arr = $buyback_arr + array('ore' => $ore_arr);
    $buyback_arr = $buyback_arr + array('mineral' => $mineral_arr);
    $buyback_arr = $buyback_arr + array('pi' => $pi_arr);
    $buyback_arr = $buyback_arr + array('total' => intval($_POST['total']));
    $buyback_arr = json_encode($buyback_arr);
    // echo $buyback_arr;
    // echo "<br>";
    // die();
    $discord_id = null;
    $stmt = $mysqli->prepare('SELECT discord_id FROM user_info WHERE user_id = ?');
    if ($stmt) {
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        $stmt->bind_result($discord_id);
        $stmt->fetch();
        $stmt->close();
    }else{
        die('db error');
    }

    if (strlen($ingame_id) > 0) {
        $query = 'INSERT INTO buyback(discord_id, web_id, ingame_id, list_json) VALUES(?, ?, ?, ?)';
    }else{
        $query = 'INSERT INTO buyback(discord_id, web_id, list_json) VALUES(?, ?, ?)';
    }
    $stmt = $mysqli->prepare($query);
    if ($stmt) {
        if (strlen($ingame_id) > 0) {
            $stmt->bind_param('isss', $discord_id, $user_id, $ingame_id, $buyback_arr);
        }else{
            $stmt->bind_param('iss', $discord_id, $user_id, $buyback_arr);
        }
        $stmt->execute();
        $stmt->close();
    }else{
        die('db error');
    }

    echo "<script>alert('성공적으로 신청되었습니다! 디스코드의 바이백 채널에 나타나기까지 최대 5분정도 소요될 수 있습니다.');location.href= '../index.php';</script>";
?>