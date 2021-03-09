<?php
    require __DIR__ . '/vendor/autoload.php';
    
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
?>


<!DOCTYPE html>
<html>
<head>
    <meta property="og:url" content="http://kpx.kro.kr/view_buyback.php">
    <meta property="og:title" content="K-PAX Corp Buyback Calculator">
    <?php include('./include/header.php'); ?>
    <script type="text/javascript" src="./js/view_buyback.js"></script>
</head>

<style type="text/css">
    input{
        border-style: none;
        height: 30px;
        border-radius: 5px;
        padding-left: 5px;
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
        text-align: center; background-color: rgb(240,240,240); padding:10px; border-radius: 10px;
        font-family: 'Nanum Gothic', sans-serif;
        font-weight: 400;
        font-size: 20px;
    }
</style>

<body>
    <?php include('./include/navbar.php') ?>
    <section class ="body">
        <div class="container_wide mt-2" style="text-align: center;">
            <h1 class="title">KPX Buyback</h1>
            <p>업데이트: <?php echo $updated; ?></p>
            <p>여기는 단순 계산용 사이트 입니다.하단에 신청 페이지의 링크가 있습니다.</p>
            <div class="col-md-6 col-sm-12">
                <table class="table-striped table-sm table table-hover" width="100%">
                    <thead>
                        <tr class="title">
                            <td colspan="5">Ore</td>
                        </tr>
                        <tr class="subtitle">
                            <td style="width: 1%"></td>
                            <td style="width: 21%">품목</td>
                            <td style="width: 28%">수량 입력</td>
                            <td style="width: 18%">개당 가격</td>
                            <td style="width: 32%">종류별 가격</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($ore_values as $row) {
                                if (strcmp($row[2], 'No Demand') != 0) {
                                    echo '<tr>';
                                    echo '<td class="'.$row[2].'"></td>';
                                    echo '<td class="material_name">'.str_replace('_',' ',$row[0])."</td>";
                                    echo '<td><input class = "buyback" type="number" id="'.$row[0].'_count" name="'.$row[0].'" style="width:100%;" onfocusout="calc(\''.$row[0].'\')"></td>';
                                    echo '<td id="'.$row[0].'_price">'.$row[1]."</td>";
                                    echo '<td id="'.$row[0].'_total_price" class="material_price">0</td>';
                                    echo "</tr>";
                                }
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
                            <td style="width: 21%">품목</td>
                            <td style="width: 28%">수량 입력</td>
                            <td style="width: 18%">개당 가격</td>
                            <td style="width: 32%">종류별 가격</td>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                            foreach ($mineral_values as $row) {
                                if (strcmp($row[2], 'No Demand') != 0) {
                                    echo '<tr>';
                                    echo '<td class="'.$row[2].'"></td>';
                                    echo '<td class="material_name">'.str_replace('_',' ',$row[0])."</td>";
                                    echo '<td><input class = "buyback" type="number" id="'.$row[0].'_count" name="'.$row[0].'" style="width:100%;" onfocusout="calc(\''.$row[0].'\')"></td>';
                                    echo '<td id="'.$row[0].'_price">'.$row[1]."</td>";
                                    echo '<td id="'.$row[0].'_total_price" class="material_price">0</td>';
                                    echo "</tr>";
                                }
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
                            <td style="width: 21%">품목</td>
                            <td style="width: 28%">수량 입력</td>
                            <td style="width: 18%">개당 가격</td>
                            <td style="width: 32%">종류별 가격</td>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                            foreach ($pi_values1 as $row) {
                                if (strcmp($row[2], 'No Demand') != 0) {
                                    echo '<tr>';
                                    echo '<td class="'.$row[2].'"></td>';
                                    echo '<td class="material_name">'.str_replace('_',' ',$row[0])."</td>";
                                    echo '<td><input class = "buyback" type="number" id="'.$row[0].'_count" name="'.$row[0].'" style="width:100%;" onfocusout="calc(\''.$row[0].'\')"></td>';
                                    echo '<td id="'.$row[0].'_price">'.$row[1]."</td>";
                                    echo '<td id="'.$row[0].'_total_price" class="material_price">0</td>';
                                    echo "</tr>";
                                }
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
                            <td style="width: 21%">품목</td>
                            <td style="width: 28%">수량 입력</td>
                            <td style="width: 18%">개당 가격</td>
                            <td style="width: 32%">종류별 가격</td>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                            foreach ($pi_values2 as $row) {
                                if (strcmp($row[2], 'No Demand') != 0) {
                                    echo '<tr>';
                                    echo '<td class="'.$row[2].'"></td>';
                                    echo '<td class="material_name">'.str_replace('_',' ',$row[0])."</td>";
                                    echo '<td><input class = "buyback" type="number" id="'.$row[0].'_count" name="'.$row[0].'" style="width:100%;" onfocusout="calc(\''.$row[0].'\')"></td>';
                                    echo '<td id="'.$row[0].'_price">'.$row[1]."</td>";
                                    echo '<td id="'.$row[0].'_total_price" class="material_price">0</td>';
                                    echo "</tr>";
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container mt-2" style="margin-top: 20px; margin-bottom: 30px;">
            <div class="col-md-4 col-sm-2"></div>
            <div class="col-md-4 col-sm-8 total_price">
                총합: <span id="total_price">0</span>&nbsp;&nbsp;ISK
            </div>
            <div class="col-md-4 col-sm-2"></div>'
        </div>
        <div class="container mt-2" style="margin-top: 20px; margin-bottom: 30px;">
            <div class="col-md-4 col-sm-2"></div>
            <div class="col-md-4 col-sm-8" style="text-align: center;">
                <button class="submit" onclick="location.href='buyback.php'">신청하러 가기</button>
            </div>
            <div class="col-md-4 col-sm-2"></div>
        </div>

    </section>
</body>
</html>