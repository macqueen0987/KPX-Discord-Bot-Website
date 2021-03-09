<section class="navbar">
    <nav class="navbar navbar-default navbar-fixed-top" style="background-color: rgb(240, 240, 240);margin-bottom: 0px; padding: 0;">
        <div class="container" style="background-color: rgb(240, 240, 240); ">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="glyphicon glyphicon-menu-hamburger"></span>
                </button>

                <h1 class="brand brand-name navbar-left"><a href="index.php" class="brand-name">KPAX</a></h1>
            </div>
            <div class="collapse navbar-collapse navbar-right" id="myNavbar">
                <ul class="nav navbar-nav">
                    <?php if ($_SESSION['LoginUserType'] == 1) {?>
                        <li>
                            <div class="dropdown">
                                <button class="dropbtn">디렉 설정 ▼</button>
                                <div class="dropdown-content">
                                    <a href="priceanddemand.php">바이백/WOP 설정</a>
                                    <!-- <a href="#">바이백 장부</a> -->
                                </div>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if(isset($_SESSION['LoginUserId'])){ ?>
                        <li><a href="buyback.php">바이백 신청</a></li>
                        <li><a href="wop.php">WOP 신청</a></li>
                    <?php }else{ ?>
                        <!-- <li><a href="wop_public.php">KPX Ship Export</a></li> -->
                        <!-- <li><a href="view_buyback.php">바이백 계산기</a></li> -->
                    <?php } ?>

                    <?php if(!isset($_SESSION['LoginUserId'])){ ?>
                        <li><a href='login.php'>Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php }else{ ?>
                        <li>
                            <div class="dropdown">
                                <button class="dropbtn"><?php echo $_SESSION['LoginUserNick'];?> ▼</button>
                                <div class="dropdown-content">
                                    <a href="user_info.php">내 정보 변경</a>
                                    <a href="#">바이백/WOP 기록보기</a>
                                    <a href="./process/logout.php">로그아웃</a>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <svg height="5" width="100%" style="position: relative; top:1px;">
          <line x1="0" y1="0" x2="0%" y2="0" id = "scroll" style="stroke:rgb(255,0,0);stroke-width:10;z-index: 90000"/>
        </svg>
    </nav>
</section>

