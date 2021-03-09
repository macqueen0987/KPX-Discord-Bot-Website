<?php
    
    include('./include/phpframe.php');
    include('./include/dbframe.php');

?>

<!DOCTYPE html>
<html>
<head>
    <?php include('./include/header.php'); ?>
    <script type="text/javascript" src="./js/index.js"></script>
    <meta property="og:url" content="http://kpx.kro.kr">
    <meta property="og:title" content="K-PAX Corp Main ">
    <link rel="stylesheet" type="text/css" href="./css/glitch.css">
</head>

<style type="text/css">
    * {box-sizing: border-box;}
    body{
        height: 100%;
        width: 100%;
        margin: 0;
    }
    section.body{
        margin: 0px !important;
        padding:0px !important;
    }
    div.banner{
        z-index: 0;
        height: 100%;
        display: none;
        width: 100%;
        margin:0px !important;
        padding:0px !important; 
        transition: all 5s;
        background-repeat: no-repeat;
        background-size: cover;
    }

    .img1{background-image: url('./img/img1.jpg');}
    .img2{background-image: url('./img/img2.jpg');}
    .img3{background-image: url('./img/img3.jpg');}
    .img4{background-image: url('./img/img4.jpg');}

    /* Fading animation */
    .fadein {
      -webkit-animation-name: fadein;
      -webkit-animation-duration: 5s;
      animation-name: fadein;
      animation-duration: 5s;
    }

    @-webkit-keyframes fadein {
      from {opacity: 0} 
      to {opacity: 1}
    }

    @keyframes fadein {
      from {opacity: 0} 
      to {opacity: 1}
    }
    .banner_h{
        position: absolute;
        color:black;
        z-index: 10;
        width: 100%;
        text-align: center;
        text-shadow: white 0px 0 20px;
        font-family: 'MuseoModerno', cursive;
    }
    div.banner_h{
        top: 40%;
    }
    h1.banner_h{
        font-size: 90px;
    }

    span.banner_h{
        font-size: 30px;
    }
    /* Fading animation */
    .fadeout {
      -webkit-animation-name: fadeout;
      -webkit-animation-duration: 5s;
      animation-name: fadeout;
      animation-duration: 5s;
    }

    @-webkit-keyframes fadeout {
      from {opacity: 1} 
      to {opacity: 0}
    }

    @keyframes fadeout {
      from {opacity: 1} 
      to {opacity: 0}
    }


</style>
<body onload="showSlides()">
    <?php include('./include/navbar.php') ?>
    <section class="body" id="body">
    <div class="banner noselect img1 fadein"></div><div class="banner noselect img1 fadeout"></div>
    <div class="banner noselect img2 fadein"></div><div class="banner noselect img2 fadeout"></div>
    <div class="banner noselect img3 fadein"></div><div class="banner noselect img3 fadeout"></div>
    <div class="banner noselect img4 fadein"></div><div class="banner noselect img4 fadeout"></div>
    <div class="banner_h">
        <h1 class="banner_h noselect glitch" data-text="K-PAX">K-PAX</h1><br><br><br><br>
        <h2 class="banner_h noselect">EVE Echoes Korean Corp</h2>
    </div>
</section>
</body>
</html>