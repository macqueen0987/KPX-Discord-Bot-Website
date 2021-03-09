<?php


    include('./include/phpframe.php');
    include('./include/dbframe.php');
    $stmt = $mysqli->prepare('SELECT * FROM buyback_price');
    $updated = $ore = $mineral = $pi1 = $pi2 = $budget = null;
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($updated, $ore, $mineral, $pi1, $pi2, $budget);
        $stmt->fetch();
        $stmt->close();
    }else{
        die($mysqli->error);
    }

    $stmt = $mysqli->prepare('SELECT * FROM WOP_buyback_stats');
    $wop_stats = $buyback_stats = null;
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($wop_stats, $buyback_stats);
        $stmt->fetch();
        $stmt->close();
    }else{
        die($mysqli->error);
    }

    $ore_values = json_decode($ore);
    for ($i=0; $i < count($ore_values); $i++) { 
        $ore_values[$i][0] = str_replace(' ', '_', $ore_values[$i][0]);
    }

    $mineral_values = json_decode($mineral);
    for ($i=0; $i < count($mineral_values); $i++) { 
        $mineral_values[$i][0] = str_replace(' ', '_', $mineral_values[$i][0]);
    }

    $pi_values1 = json_decode($pi1);
    for ($i=0; $i < count($pi_values1); $i++) { 
        $pi_values1[$i][0] = str_replace(' ', '_', $pi_values1[$i][0]);
    }


    $pi_values2 = json_decode($pi2);
    for ($i=0; $i < count($pi_values2); $i++) { 
        $pi_values2[$i][0] = str_replace(' ', '_', $pi_values2[$i][0]);
    }

    $pi_values = array_merge($pi_values1, $pi_values2);
    
    $num = 0;

    print(json_encode($pi_values));
?>