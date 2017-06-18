<?php include("meta.inc.html");?>
        <title>借用狀態查詢 - 佛光大學學生席次借用系統</title>
        <link rel="stylesheet" href="/css/pending.css" />
<?php include("header.inc.html");?>
                <div id="status_detail" class="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">借用狀態查詢</h4>
                            </div>
                            <div class="modal-body">
                                <p>以下為借用詳細資料：</p>
                                <p>場地：<span></span></p>
                                <p>日期：<span></span></p>
                                <p>時段：<span></span></p>
                                <p>申請單位：<span></span></p>
                                <p>申請人：<span></span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
                            </div>
                        </div>
                    </div>  
                </div>
                <section class="content-header">
                    <h1>借用狀態查詢</h1>
                    <ol class="breadcrumb">
                        <li><a href="#">佛光大學學生席次借用系統</a></li>
                        <li class="active"><a href="status.php">借用狀態查詢</a></li>
                    </ol>
                </section>
                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box box-default">
                                <div id="status_calendar" class="box-body">
                                    <div class="table table-bordered table-striped"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="box box-default">
                                <div id="status" class="box-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>席次</th>
                                                <th class="status-time">狀態</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="15">
                                                    圖例：
                                                   <!-- <span class="status-figure label-primary" title="審核中">
                                                        <i class="fa fa-fw fa-hourglass-start"></i> 審核中
                                                    </span> -->
                                                    <span class="status-figure label-success" title="已通過">
                                                        <i class="fa fa-fw fa-check"></i> 已通過
                                                    </span>
                                                    <span class="status-figure label-danger" title="不開放">
                                                        <i class="fa fa-fw fa-ban"></i> 不開放
                                                    </span>
                                                </th>
                                            </tr>
                                        </tfoot>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
                <script src="/datepicker/js/bootstrap-datepicker.min.js"></script>
                <script src="/datepicker/locales/bootstrap-datepicker.zh-TW.min.js"></script>
                <script src="/js/status.js?v=0914a0c"></script>
                <script src="/js/pending.js"></script>
<?php include("footer.inc.html");?>