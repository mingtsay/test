<?php 

	include_once('include.inc.php');

    $redirect_query = isset($_SESSION['admin_query']) ? ('?' . $_SESSION['admin_query']) : '';

    if(!$_SESSION['admin_login']) redirect('login.php') ;
    if(time() - $_SESSION['admin_ts']<=600) redirect($redirect_query);

    $okay = true;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_SESSION['admin_root'] ? 'root' : 'admin';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $okay = false;
        if ($username === 'root' && hash('sha256', $password . 'qDizIBEx') === 'a42d45917a0877d961c9d5361177d16608a6c1f0f83c36f9c286b43c4d116504') {
            $okay = true;
            $_SESSION['admin_ts'] = time();
            redirect($redirect_query);
        } else if ($username === 'admin' && $password === 'acc1661') {
            $okay = true;
            $_SESSION['admin_ts'] = time();
            redirect($redirect_query);
        }
    }

    $html = '<!DOCTYPE html>
<html lang="zh-tw">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>輸入密碼 | 佛光大學座位預約系統</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
        <meta name="theme-color" content="#d2d6de" />
        <meta name="msapplication-navbutton-color" content="#d2d6de" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
        <link rel="stylesheet" href="dist/css/AdminLTE.min.css" />
        <link rel="icon" type="image/png" href="/images/logo.png" />
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->' . ($okay ? '' : '
        <style>.lockscreen-credentials,.lockscreen-credentials .input-group,.lockscreen-credentials .input-group-btn,.lockscreen-credentials .form-control,.lockscreen-credentials .btn,.lockscreen-credentials .text-muted{background:#fcc;color:#fff}</style>') . '
    </head>
    <body class="hold-transition lockscreen">
        <!-- Automatic element centering -->
        <div class="lockscreen-wrapper">
            <div class="lockscreen-logo">
                <a href="./"><b>FGU</b>SG</a>
            </div>
            <!-- User name -->
            <div class="lockscreen-name">' . ($_SESSION['admin_root'] ? 'root' : 'admin') . '</div>
            <!-- START LOCK SCREEN ITEM -->
            <div class="lockscreen-item">
                <!-- lockscreen image -->
                <div class="lockscreen-image">
                    <img src="/images/logo.png" alt="FGU Logo">
                </div>
                <!-- /.lockscreen-image -->
                <!-- lockscreen credentials (contains the form) -->
                <form class="lockscreen-credentials" action="lockscreen.php" method="post">
                    <div class="input-group">
                        <input name="password" type="password" class="form-control" placeholder="' . ($okay ? '請輸入密碼' : '密碼錯誤，請再試一次') . '">
                        <div class="input-group-btn">
                            <button class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
                        </div>
                    </div>
                </form><!-- /.lockscreen credentials -->
            </div><!-- /.lockscreen-item -->
            <div class="help-block text-center">
                您已超過十分鐘未執行任何操作，<br />
                請重新輸入您的密碼來繼續使用後台。
            </div>
            <div class="text-center">
                <p><a href="logout.php">或點選此處登出 <i class="fa fa-sign-out"></i></a></p>
                <p><a href="/">&laquo; 返回 佛光大學座位預約系統</a></p>
            </div>
            <div class="lockscreen-footer text-center">
                Copyright &copy; 2017 <a href="/" title="佛光大學學生會">FGUSG</a>.<br />
                All rights reserved.
            </div>
        </div><!-- /.center -->
        <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>' . ($okay ? '' : '
        <script>$(function(){var l=30;for(var i=0;i<6;++i)$(".lockscreen-wrapper").animate({"padding-left":"+="+(l=-l)+"px","padding-right":"-="+l+"px"},60);$(".lockscreen-wrapper").animate({"padding-left":"0px","padding-right":"0px"},60);});</script>') . '
    </body>
</html>
';
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Length: ' . strlen($html));
    echo($html);