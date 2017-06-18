<?php include("meta.inc.html");?>
        <title>登入 - 佛光大學席位預約系統</title>
<?php include("header.inc.html");?>
                <section class="content-header">
                    <h1>登入</h1>
                    <ol class="breadcrumb">
                        <li><a href="#">佛光大學席位預約系統</a></li>
                        <li class="active"><a href="login.php">登入</a></li>
                    </ol>
                </section>
                <section class="content">
                    <div class="row">
                        <div class="col-lg-6 col-md-8 col-sm-12 col-lg-offset-3 col-md-offset-2">
                            <script src="//www.google.com/recaptcha/api.js"></script>
                            <form id="form_login" role="form">
                                <div id="login_failure" class="alert alert-danger" style="display: none">
                                    <h4><i class="icon fa fa-user-times"></i> 登入失敗！</h4>
                                    請確認您輸入的登入資訊是否正確。
                                </div>
                                <div class="box box-primary">
                                    <div class="box-body">
                                        <div class="col-md-12">
                                            <div class="form-group has-feedback">
                                                <input class="form-control" name="sid" placeholder="請輸入您的學號" />
                                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                            </div>
                                            <div class="form-group has-feedback">
                                                <input class="form-control has-feedback" name="pw" type="password" placeholder="請輸入學生資訊系統密碼" />
                                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div id="recaptcha" class="g-recaptcha form-control" data-sitekey="6Lf5jCUUAAAAAFJjQZ_6qjkBaYmOFCw0Jn9ZqdAY"></div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">登入</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
                <script src="/js/login.js"></script>
<?php include("footer.inc.html");?>