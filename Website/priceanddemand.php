<?php
    require  './vendor/autoload.php';

    include('./include/phpframe.php');
    include('./include/check_logged.php');
    include('./include/dbframe.php');
    include('./include/check_director.php');

    function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        $client->setAuthConfig('./google_api/credential_macqueen0987.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');


        $tokenPath = './google_api/token_macqueen0987.json';
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
        return $client;
    }


    $client = getClient();
    $service = new Google_Service_Sheets($client);

    $range = array('Hanger_Input!I53:I60', 'Hanger_Input!I69:I84');
    $pi_arr = array();
    $response = $service->spreadsheets_values->get($id_sheet, 'Hanger_Input!I3:I36');
    $value = $response->getValues();

    $pi_arr = array_fill(0, count($value), 0);

    $num_arr = json_decode("[[0, 16], [1, 28], [2, 8], [3, 2], [4, 25], [5, 17], [6, 7], [7, 15], [8, 21], [9, 9], [10, 5], [11, 6], [12, 26], [13, 19], [14, 0], [15, 10], [16, 20], [17, 27], [18, 33], [19, 12], [20, 31], [21, 24], [22, 4], [23, 1], [24, 3], [25, 18], [26, 29], [27, 30], [28, 11], [29, 32], [30, 14], [31, 13], [32, 22], [33, 23]]", true);

    foreach ($num_arr as $num) {
        $pi_arr[$num[1]] = $value[$num[0]][0];
    }

    $arrays = array();
    for ($i=0; $i < count($range); $i++) { 
        $response = $service->spreadsheets_values->get($id_sheet, $range[$i]);
        $value = $response->getValues();
        array_push($arrays, $value);
    }


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


    $stmt = $mysqli->prepare('SELECT updated, price_json, banned FROM WOP_price');
    $updated = $wop_arr = $banned = null;
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($updated, $wop_arr, $banned);
        $stmt->fetch();
        $stmt->close();
    }else{
        die($mysqli->error);
    }
    $wop_arr = json_decode($wop_arr);
    $banned = json_decode($banned);


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
    
    $wop_arr_temp = array();
    foreach ($wop_arr as $value) {
        $wop_arr_temp = array_merge($wop_arr_temp, $value);
    }
    $wop_arr = $wop_arr_temp;
    $cnt = count($wop_arr);
    $cnt = intdiv($cnt, 4);
    $left = array();
    $left = array_fill(0, 4, false);

    if ($cnt == 1) {
        $left[3] = true;
    }
    if ($cnt == 2) {
        $left[3] = true;
        $left[2] = true;
    }
    if ($cnt == 3) {
        $left[1] = true;
        $left[2] = true;
        $left[3] = true;
    }

    $num = 0;
?>


<!DOCTYPE html>
<html>
<head>
    <?php include('./include/header.php'); ?>
    <script type="text/javascript" src="./js/priceanddemand.js"></script>
</head>

<style type="text/css">
    .col-sm-6, .col-md-3{
        padding-right: 5px;
        padding-left: 5px;
    }
    td.left{
        width: 51%;
        text-align: left;
        padding-left: 10px;
        font-size: 13px;
    }
    td.mid{
        width: 45%;
        text-align: right;
        padding-right: 10px;
        font-size: 13px;
    }
    td.right{
        width: 4%;
        text-align: center;
        padding: 0 !important;
    }
    td.product_name{
        text-align: left;
        padding-left: 10px;
    }
    td.product_price{
        text-align: right;
        padding-right: 10px;
    }


    input{
        border-style: none;
        height: 35px;
        border-radius: 5px;
        padding-left: 5px;
        font-size: 15px;
        padding-right: 5px;
    }
    input.buyback{
        font-size: 12px;
    }
    input:focus{
        outline: none;
        box-shadow: 0 0 5px rgba(255, 10, 81, 1);
        border-color: red;
    }
    div.total_price{
        text-align: left; background-color: rgb(240,240,240); padding:10px; border-radius: 10px;
        padding-left: 25px;
        font-family: 'Nanum Gothic', sans-serif;
        font-weight: 400;
        }
</style>

<body>
    <?php include('./include/navbar.php') ?>
    <section class ="body">
        <form action="./process/priceanddemand.php" method="post">
            <div class="container_wide mt-2">
                <h1 class="title">KPX Buyback Setting</h1>
                <div class="col-md-6 col-sm-12">
                    <table class="table-striped table-sm table table-hover" width="100%">
                        <thead>
                            <tr class="title">
                                <td colspan="5">Ore</td>
                            </tr>
                            <tr class="subtitle">
                                <td style="width: 1%"></td>
                                <td style="width: 33%">품목</td>
                                <td style="width: 33%">우선순위</td>
                                <td style="width: 33%">행어 재고</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $num = 0;
                                foreach ($ore_values as $row) {
                                    echo '<tr>';
                                    echo '<td class="'.str_replace(' ','_',$row[2]).'" id = "'.$row[0].'"></td>';
                                    echo '<td class="material_name">'.str_replace('_',' ',$row[0])."</td>";
                                    echo '<td><input type="text" id="'.$row[0].'_demand" name="ore_'.$row[0].'_demand" style="width:100%;" value="'.$row[2].'" list="demands" onfocus="this.value=\'\'" onfocusout="demand(\''.$row[0].'\')"></td>';
                                    echo '<td><input type="number" id="'.$row[0].'_quant" name="mineral_'.$row[0].'_quant" style="width:100%;" value="'.intval(str_replace(',', '', $arrays[1][$num][0])).'"></td>';
                                    echo "</tr>";
                                    $num ++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 col-sm-12">
                    <table class="table-striped table-sm table table-hover" width="100%">
                        <thead>
                            <tr class="title">
                                <td colspan="5">Mineral</td>
                            </tr>
                            <tr class="subtitle">
                                <td style="width: 1%"></td>
                                <td style="width: 33%">품목</td>
                                <td style="width: 33%">우선순위</td>
                                <td style="width: 33%">행어 재고</td>
                            </tr>

                        </thead>
                        <tbody>
                            <?php
                                $num = 0;
                                foreach ($mineral_values as $row) {
                                    echo '<tr>';
                                    echo '<td class="'.str_replace(' ','_',$row[2]).'" id = "'.$row[0].'"></td>';
                                    echo '<td class="material_name">'.str_replace('_',' ',$row[0])."</td>";
                                    echo '<td><input type="text" id="'.$row[0].'_demand" name="mineral_'.$row[0].'_demand" style="width:100%;" value="'.$row[2].'" list="demands" onfocus="this.value=\'\'" onfocusout="demand(\''.$row[0].'\')"></td>';
                                    echo '<td><input type="number" id="'.$row[0].'_quant" name="mineral_'.$row[0].'_quant" style="width:100%;" value="'.intval(str_replace(',', '', $arrays[0][$num][0])).'"></td>';
                                    echo "</tr>";
                                    $num ++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="container_wide mt-2" style="margin-top: 20px;">
                <div class="col-md-6 col-sm-12">
                    <table class="table-striped table-sm table table-hover" width="100%">
                        <thead>
                            <tr class="title">
                                <td colspan="5">PI 1</td>
                            </tr>
                            <tr class="subtitle">
                                <td style="width: 1%"></td>
                                <td style="width: 33%">품목</td>
                                <td style="width: 33%">우선순위</td>
                                <td style="width: 33%">행어 재고</td>
                            </tr>

                        </thead>
                        <tbody>
                            <?php
                                $num = 0;
                                foreach ($pi_values1 as $row) {
                                    echo '<tr>';
                                    echo '<td class="'.str_replace(' ','_',$row[2]).'" id = "'.$row[0].'"></td>';
                                    echo '<td class="material_name">'.str_replace('_',' ',$row[0])."</td>";
                                    echo '<td><input type="text" id="'.$row[0].'_demand" name="pi1_'.$row[0].'_demand" style="width:100%;" value="'.$row[2].'" list="pi_demands" onfocus="this.value=\'\'" onfocusout="demand(\''.$row[0].'\')"></td>';
                                    echo '<td><input type="number" id="'.$row[0].'_quant" name="pi1_'.$row[0].'_quant" style="width:100%;" value="'.intval(str_replace(',', '', $pi_arr[$num])).'"></td>';
                                    echo "</tr>";
                                    $num ++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 col-sm-12">
                    <table class="table-striped table-sm table table-hover" width="100%">
                        <thead>
                            <tr class="title">
                                <td colspan="5">PI 2</td>
                            </tr>
                            <tr class="subtitle">
                                <td style="width: 1%"></td>
                                <td style="width: 33%">품목</td>
                                <td style="width: 33%">우선순위</td>
                                <td style="width: 33%">행어 재고</td>
                            </tr>

                        </thead>
                        <tbody>
                            <?php

                                foreach ($pi_values2 as $row) {
                                    echo '<tr>';
                                    echo '<td class="'.str_replace(' ','_',$row[2]).'" id = "'.$row[0].'"></td>';
                                    echo '<td class="material_name">'.str_replace('_',' ',$row[0])."</td>";
                                    echo '<td><input type="text" id="'.$row[0].'_demand" name="pi2_'.$row[0].'_demand" style="width:100%;" value="'.$row[2].'" list="pi_demands" onfocus="this.value=\'\'" onfocusout="demand(\''.$row[0].'\')"></td>';
                                    echo '<td><input type="number" id="'.$row[0].'_quant" name="pi2_'.$row[0].'_quant" style="width:100%;" value="'.intval(str_replace(',', '', $pi_arr[$num])).'"></td>';
                                    echo "</tr>";
                                    $num ++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="container mt-2" style="margin-top: 20px;">
                <div class="col-md-2 col-sm-0"></div>
                <div class="col-md-4 col-sm-12" style="text-align: center;">
                    <button type="submit" class="submit">바이백 적용하기</button><br>
                    <button type="button" class="submit" onclick="location.href='./process/get_buyback.php'">시트값 가져오기</button>
                </div>
                <div class="col-md-4 col-sm-12" style="text-align: center;">
                    <button type="button" class="submit" id="buyback_stats" onclick="change_buyback()"><?php if($buyback_stats) {echo "바이백 비활성화";} else{echo "바이백 활성화";} ?></button>
                </div>
                <div class="col-md-2 col-sm-0"></div>
            </div>
            <datalist id="demands">
                <option value="High">
                <option value="Medium">
                <option value="Minimal">
                <option value="No Demand">
            </datalist>
            <datalist id="pi_demands">
                <option value="Urgent">
                <option value="High">
                <option value="Medium">
                <option value="Minimal">
                <option value="No Demand">
            </datalist>
        </form>
        <hr>
        <div class="container mt-2" style="text-align: center;">
            <h1 class="title">KPX WOP Setting</h1>
            <p>체크된 항목은 WOP 에서 제외되는 것들임</p>
        </div>

        <form action="./process/ban_wop.php" method="post">
            <div class="container_wide mt-2">
                <?php $num = 0; for ($i=0; $i < 4; $i++) { 
                    $j = 0; ?>
                    <div class="col-sm-6 col-md-3">
                        <table class="table-striped table-sm table table-hover" width="100%">
                            <thead>
                                <tr class="title">
                                    <td>품목</td>
                                    <td>가격</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php for ($j=0; $j < $cnt; $j++) { ?>
                                <tr>
                                    <td class=<?php echo '"left product_name'.($i+1).'"' ?> id=<?php echo '"'.str_replace(' ', '_', $wop_arr[$num][0]).'"'; ?>><?php echo $wop_arr[$num][0]; ?></td>
                                    <td class=<?php echo '"mid product_price'.($i+1).'"' ?> id=<?php echo '"'.str_replace(' ', '_', $wop_arr[$num][0]).'_price"'; ?>><?php echo $wop_arr[$num][1]; ?></td>
                                    <td class="right"><input type="checkbox" name=<?php echo '"'.str_replace(' ', '_', $wop_arr[$num][0]).'"'; ?> <?php if(in_array(str_replace(' ', '_', $wop_arr[$num][0]), $banned)) {echo "checked";} ?>></td>
                                </tr>
                            <?php $num++; } ?>
                            <?php if ($left[$i]) { ?>
                                <tr>
                                    <td class=<?php echo '"left product_name'.($i+1).'"' ?> id=<?php echo '"'.str_replace(' ', '_', $wop_arr[$num][0]).'"'; ?>><?php echo $wop_arr[$num][0]; ?></td>
                                    <td class=<?php echo '"mid product_price'.($i+1).'"' ?> id=<?php echo '"'.str_replace(' ', '_', $wop_arr[$num][0]).'_price"'; ?>><?php echo $wop_arr[$num][1]; ?></td>
                                    <td class="right"><input type="checkbox" name=<?php echo '"'.str_replace(' ', '_', $wop_arr[$num][0]).'"'; ?> <?php if(in_array(str_replace(' ', '_', $wop_arr[$num][0]), $banned)) {echo "checked";} ?>></td>
                                </tr>
                            <?php $num ++; } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
            <div class="container mt-2" style="text-align: center;">
                <button type="submit" class="submit">적용하기</button>
            </div>
        </form>


        <div class="container mt-2">
            <div class="col-md-12 col-sm-12" style="text-align: center;">
                <button type="button" class="submit" id="wop_stats" onclick="change_wop()"><?php if($wop_stats) {echo "WOP 비활성화";} else{echo "WOP 활성화";} ?></button>
            </div>
        </div>
        <div class="container mt-2">
            <div class="col-md-12 col-sm-12" style="text-align: center;">
                <button type="button" class="submit" onclick="location.href='./process/get_wop.php'">시트값 가져오기</button>
            </div>
        </div>
    </section>
</body>
</html>