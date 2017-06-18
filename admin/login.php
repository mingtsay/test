<?php
    include_once('include.inc.php');
    function recaptcha_verify($recaptcha_response) {
        return json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create(array('http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query(array(
                'secret'   => '6Le0QRYTAAAAACiX_DKhiDRSLPl1JXVPfMZB-hX7',
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
        )))))), true)['success'];
    }
    if ($_SESSION['admin_login']) redirect('');
    $okay = true;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $recaptcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
        $okay = false;
        if (recaptcha_verify($recaptcha)) {
            if ($username === 'root' && hash('sha256', $password . 'qDizIBEx') === 'a42d45917a0877d961c9d5361177d16608a6c1f0f83c36f9c286b43c4d116504') {
                $okay = true;
                $_SESSION['admin_login'] = true;
                $_SESSION['admin_root'] = true;
                $_SESSION['admin_ts'] = time();
                redirect('');
            } else if ($username === 'admin' && $password === 'acc1661') {
                $okay = true;
                $_SESSION['admin_login'] = true;
                $_SESSION['admin_root'] = false;
                $_SESSION['admin_ts'] = time();
                redirect('');
            }
        }
    }
    $msg_class = $okay ? '' : ' login-failure';
    $msg = $okay ? '佛光大學座位預約系統後台' : (mt_rand(0, 9) ? '登入失敗，請檢查您輸入的資料是否正確' : '哼～人家才不讓你登入哩030');
    $html = '<!DOCTYPE html>
<html lang="zh-tw">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>後台登入 | 佛光大學座位預約系統</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
        <meta name="theme-color" content="#d2d6de" />
        <meta name="msapplication-navbutton-color" content="#d2d6de" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
        <link rel="stylesheet" href="dist/css/AdminLTE.min.css" />
        <link rel="stylesheet" href="plugins/iCheck/square/blue.css" />
        <link rel="icon" type="image/png" href="/images/logo.png" />
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            .g-recaptcha {
                display: table;
                left: 50%;
                margin: 8px -152px;
                position: relative;
                right: 50%;
            }
            .login-box.login-failure .login-box-msg {
                color: #f00;
            }
        </style>
        <script src="//www.google.com/recaptcha/api.js"></script>
    </head>
    <body class="hold-transition login-page">
        <div class="login-box' . $msg_class . '">
            <div class="login-logo">
                <a href="./"><b>NTUST</b>SG</a>
            </div><!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">' . $msg . '</p>
                <form action="login.php" method="post">
                    <div class="form-group has-feedback">
                        <input name="username" class="form-control" placeholder="帳號" />
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input name="password" type="password" class="form-control" placeholder="密碼" />
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <!--div class="checkbox icheck">
                                <label>
                                    <input type="checkbox" /> 記住帳號密碼
                                </label>
                            </div-->
                        </div><!-- /.col -->
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">登入</button>
                        </div><!-- /.col -->
                    </div>
                    <div class="g-recaptcha text-center" data-sitekey="6Lf5jCUUAAAAAFJjQZ_6qjkBaYmOFCw0Jn9ZqdAY">
                    </div><!-- /.g-recaptcha -->
                </form>
                <a href="/">&laquo; 返回 佛光大學座位預約系統</a>
            </div><!-- /.login-box-body -->
        </div><!-- /.login-box -->
        <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="plugins/iCheck/icheck.min.js"></script>
        <script>
            $(function () {
                $(\'input\').iCheck({
                    checkboxClass: \'icheckbox_square-blue\',
                    radioClass: \'iradio_square-blue\',
                    increaseArea: \'20%\' // optional
                });
            });
        </script>
    </body>
</html>
';
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Length: ' . strlen($html));
    echo($html);