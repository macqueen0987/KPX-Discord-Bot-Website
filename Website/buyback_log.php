<?php 
    include('./include/phpframe.php');
    include('./include/check_logged.php');
?>

<!DOCTYPE html>
<html>
<head>
    <?php include('./include/header.php'); ?>
</head>

<style type="text/css">
    table{
        font-family: 'Nanum Gothic', sans-serif;
        background-color: rgb(240,240,240);
        text-align: center;
    }
    thead{
        color: white;
        border-style: none;
    }
    tr.title{
        /*text-align: center;*/
        background-color: rgb(80,80,80);
        font-weight: 700;
        font-size: 25px;
    }
    tr.subtitle{
        background-color: rgb(150,150,150);
        font-size: 15px;
    }
</style>

<body>
    <?php include('./include/navbar.php') ?>
    <section class ="body">
        <div class="container mt-2" style="padding-top: 20px;">
            <table class=" table-bordered table table-hover" style="width: 100%;">
                <thead>
                    <tr class="title">
                        <td colspan="8">바이백 일지</td>
                    </tr>
                    <tr class="subtitle">
                        <td style="width: 5%">일시</td>
                        <td style="width: 15%">닉네임</td>
                        <td style="width: 9%">입금</td>
                        <td style="width: 9%">출금</td>
                        <td style="width: 9%">판매금액</td>
                        <td style="width: 9%">세금</td>
                        <td style="width: 9%">잔고</td>
                        <td style="width: 35%">비고</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>20.10.20</td>
                        <td>macqueen0987</td>
                        <td>1000000000</td>
                        <td>000</td>
                        <td>000</td>
                        <td>000</td>
                        <td>000</td>
                        <td>000</td>
                    </tr>
                    <tr>
                        <td>20.10.20</td>
                        <td>macqueen0987</td>
                        <td>1000000000</td>
                        <td>000</td>
                        <td>000</td>
                        <td>000</td>
                        <td>000</td>
                        <td>000</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </section>
</body>
</html>