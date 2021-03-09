<?php

    include('./include/phpframe.php');
    include('./include/dbframe.php');
    include('./include/check_logged.php');

    $stmt = $mysqli->prepare('SELECT updated, kr_json FROM WOP_price');
    $updated = $arr = null;
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($updated, $arr);
        $stmt->fetch();
        $stmt->close();
    }else{
        die($mysqli->error);
    }

    $arr = json_decode($arr);

    $stmt = $mysqli->prepare('SELECT WOP FROM WOP_buyback_stats');
    $stats = null;
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($stats);
        $stmt->fetch();
        $stmt->close();
    }else{
        die($mysqli->error);
    }

    $num = 0;
?>


<!DOCTYPE html>
<html>
<head>
    <meta property="og:url" content="http://kpx.kro.kr/wop_kr.php">
    <meta property="og:title" content="K-PAX Corp Korean Export">
    <?php include('./include/header.php'); ?>
    <script type="text/javascript" src="./js/wop.js"></script>
</head>

<style type="text/css">
    .col-sm-6, .col-md-3{
        padding-right: 5px;
        padding-left: 5px;
    }
    td.left{
        width: 55%;
        text-align: left;
        padding-left: 10px;
    }
    td.right{
        width: 45%;
        text-align: right;
        padding-right: 10px;
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
        font-size: 20px;
    }
    .hit{
        background-color: rgb(255,200,200);
    }
</style>

<body>
    <?php include('./include/navbar.php') ?>
    <section class ="body">
        <div class="container" style="margin-bottom: 20px; text-align: center;">
            <h1 class="title">KPX WOP</h1>
            <p>업데이트 일시: <?php echo $updated; ?></p>
            <p>※품목 클릭시에도 선택됩니다※</p>
            <div class="col-md-10 col-sm-8"></div>
            <div class="col-md-2 col-sm-4">
                <input type="text" name="search" placeholder="함선 검색" id="search" style="border: solid red 1px;" autofocus autocomplete="off" onkeyup="search()">
            </div>
        </div>
        <div class="container_wide mt-2">

            <?php foreach ($arr as $value) { $num = $num + 1;?>
            <div class="col-sm-6 col-md-3">
                <table class="table-striped table-sm table table-hover" width="100%">
                    <thead>
                        <tr class="title">
                            <td>품목</td>
                            <td>가격</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($value as $i) {?>
                        <tr>
                            <td class=<?php echo '"left product_name'.$num.'"' ?> id=<?php echo '"'.str_replace(' ', '_', $i[0]).'"'; ?> onclick="select(<?php echo "'".str_replace(' ', '_', $i[0])."'"; ?>)"><?php echo $i[0]; ?></td>
                            <td class=<?php echo '"right product_price'.$num.'"' ?> id=<?php echo '"'.str_replace(' ', '_', $i[0]).'_price"'; ?>><?php echo $i[1]; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>

        </div>
        <form action="./process/wop_export.php" method="post">
            <div class="container" style="text-align: center;">
                <div class="col-md-4 col-sm-2"></div>
                <div class="col-md-4 col-sm-8 total_price">
                    WOP 품목: <input type="text" name="WOP" style="width: 200px;margin-left: 28px;" id="wop_product" list="wop" required>
                    <datalist id="wop" >
                        <?php foreach ($arr as $value) {?>
                            <?php foreach ($value as $i) {?>
                                <option value=<?php echo '"'.$i[0].'"'; ?>></option>
                            <?php } ?>
                        <?php } ?>
                    </datalist>
                    <br><br>
                    인게임 아이디: <input type="text" name="ingame_id" style="width: 200px;">
                </div>
                <div class="col-md-4 col-sm-2"></div>
            </div>
                <div class="container mt-2" style="margin-top: 20px;">
                    <div class="col-md-12 col-sm-12" style="text-align: center;">
                        <button type=<?php if($stats) {echo '"submit"';} else{echo "button";} ?> class=<?php if($stats) {echo '"submit"';} else{echo "disabled";} ?>><?php if($stats) {echo '신청하기';} else{echo "현재는 신청이 마감되었습니다.";} ?></button>
                    </div>
                </div>
        </form>
    </section>
</body>
</html>