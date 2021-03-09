<?php

    include('./include/phpframe.php');
    include('./include/check_logged.php');
    include('./include/dbframe.php');
    include('./include/check_kpx.php');

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

    $stmt = $mysqli->prepare('SELECT buyback FROM WOP_buyback_stats');
    $stats = null;
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($stats);
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
    <meta property="og:url" content="http://kpx.kro.kr/buyback.php">
    <meta property="og:title" content="K-PAX Corp Buyback">
    <?php include('./include/header.php'); ?>
    <script type="text/javascript" src="./js/buyback.js"></script>
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

    svg.arrow{
        width: 200px; height: 20px; margin: none; display: inline-flex; flex-wrap: wrap; white-space: nowrap;
    }
</style>

<body>
    <?php include('./include/navbar.php') ?>
    <section class ="body">
        <div class="container_wide mt-2" style="text-align: center;">
            <h1 class="title">KPX Buyback</h1>
            <p>업데이트 일시: <?php echo $updated; ?></p>
            <p>※ 긴급매입은 PI에만 적용되며 원래가격의 10% 프리미엄이 부여됩니다 ※</p>
            <div class="col-md-3 col-sm-0"></div>
            <div class="col-md-6 col-sm-12">
                <table  class="table-sm table table-hover" width="100%">
                    <tbody>
                        <tr>
                            <td style="width: 20%">긴급 매입</td>
                            <td style="width: 20%">집중 매입</td>
                            <td style="width: 20%">예산내 매입</td>
                            <td style="width: 20%">매입 최하위</td>
                            <td style="width: 20%">매입 안함</td>
                        </tr>
                        <tr>
                            <td class="Urgent"></td>
                            <td class="High"></td>
                            <td class="Medium"></td>
                            <td class="Minimal"></td>
                            <td class="No_Demand"></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="display: inline;">
                                수요 높음
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 20" class="arrow">
                                        <defs>
                                            <marker id="startarrow" markerWidth="8" markerHeight="8" refX="4" refY="4" orient="auto">
                                                <polygon points="8 0, 8 8, 0 4" fill="red" />
                                            </marker>
                                            <marker id="endarrow" markerWidth="8" markerHeight="8" refX="0" refY="4" orient="auto" markerUnits="strokeWidth">
                                                <polygon points="0 0, 8 4, 0 8" fill="yellow" />
                                            </marker>
                                            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" style="stop-color:rgb(255,0,0);stop-opacity:1" />
                                                <stop offset="50%" style="stop-color:rgb(0,255,0);stop-opacity:1" />
                                                <stop offset="100%" style="stop-color:rgb(255,255,0);stop-opacity:1" />
                                            </linearGradient>
                                        </defs>
                                        <rect x=10 y=9 width=180 height=2 fill='url(#grad1)' />
                                        <line x1="10" y1="10" x2="190" y2="10" marker-end="url(#endarrow)" marker-start="url(#startarrow)" />
                                    </svg>
                                수요 낮음
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3 col-sm-0"></div>
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
                                    echo '<td id="'.$row[0].'_total_price" class="ore_material_price">0</td>';
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
                                    echo '<td id="'.$row[0].'_total_price" class="mineral_material_price">0</td>';
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
                                    echo '<td id="'.$row[0].'_total_price" class="pi_material_price">0</td>';
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
                                    echo '<td id="'.$row[0].'_total_price" class="pi_material_price">0</td>';
                                    echo "</tr>";
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <form action="./process/buyback.php" method="post">
            <?php foreach ($ore_values as $row) {
                if (strcmp($row[2], 'No Demand') != 0) { ?>
                    <input type="hidden" id=<?php echo '"'.$row[0].'_submit_quantity"'; ?> name=<?php echo'"ore_'.$row[0].'"'; ?> value="0" >
                <?php }
            } ?>
            <?php foreach ($mineral_values as $row) {
                if (strcmp($row[2], 'No Demand') != 0) { ?>
                    <input type="hidden" id=<?php echo '"'.$row[0].'_submit_quantity"'; ?> name=<?php echo'"mineral_'.$row[0].'"'; ?> value="0" >
                <?php }
            } ?>
            <?php foreach ($pi_values as $row) {
                if (strcmp($row[2], 'No Demand') != 0) { ?>
                    <input type="hidden" id=<?php echo '"'.$row[0].'_submit_quantity"'; ?> name=<?php echo'"pi_'.$row[0].'"'; ?> value="0" >
                <?php }
            } ?>
            <input type="hidden" name="ore_total" id="ore_submit_total" value="0">
            <input type="hidden" name="mineral_total" id="mineral_submit_total" value="0">
            <input type="hidden" name="pi_total" id="pi_submit_total" value="0">
            <input type="hidden" id="submit_total_price" name="total" value="0">
            <div class="container mt-2" style="margin-top: 20px;">
                <div class="col-md-4 col-sm-2"></div>
                <div class="col-md-4 col-sm-8 total_price">
                    인게임 아이디: <input type="text" name="ingame_id" placeholder="비워놓으면 기본 ID" style="width: 200px;">
                </div>
                <div class="col-md-4 col-sm-2"></div>
            </div>
            <div class="container mt-2" style="margin-top: 20px;">
                <div class="col-md-4 col-sm-2"></div>
                <div class="col-md-4 col-sm-8 total_price">
                    총합: <span id="total_price">0</span>&nbsp;&nbsp;ISK
                </div>
                <div class="col-md-4 col-sm-2"></div>
            </div>
<!--             <div class="container mt-2" style="margin-top: 20px;">
                <div class="col-md-4 col-sm-2"></div>
                <div class="col-md-4 col-sm-8 total_price">
                    현재 잔여 예산: <span style=<?php if($budget < 0){echo "\"color:red;\"";} else{echo "\"color:#00ff00;\"";} ?>><?php echo number_format($budget); ?></span>&nbsp;&nbsp;ISK
                </div>
                <div class="col-md-4 col-sm-2"></div>
            </div> -->
            <div class="container mt-2" style="margin-top: 20px;">
                <div class="col-md-12 col-sm-12" style="text-align: center;">
                        <button type=<?php if($budget < 0){echo '"button"';}elseif($stats) {echo '"submit"';} else{echo '"button"';} ?> class=<?php if($budget < 0) {echo '"disabled"';} elseif($stats) {echo "\"submit\"";} else{echo "\"disabled\"";} ?>><?php if($stats) {echo '신청하기';} elseif($budget < 0) {echo "잔여 예산이 없습니다.";} else{echo "현재는 신청이 마감되었습니다.";} ?></button>
                </div>
            </div>
        </form>
    </section>
</body>
</html>