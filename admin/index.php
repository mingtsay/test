
<?php
	echo ini_get('display_errors');

if (!ini_get('display_errors')) {
    ini_set('display_errors', '1');
}

echo ini_get('display_errors');


	include_once('include.inc.php') ;
	if(!$_SESSION['admin_login']) redirect('login.php') ;
	if(time()-$_SESSION['admin_ts'] > 600) redirect('lockscreen.php');
	$_SESSION['admin_ts'] = time() ;

	$pages = array(
		'main' 			=> '首頁',
		'block' 		=> '黑名單',
		'config' 		=> '座位詳細設定 ', //設定座位的細部設定 例如部分時段不開放
		'application' 	=> '申請紀錄',
		'notice'		=> '申請須知',
		'seat'			=> '座位設定', //是拿來新增修改名稱跟停用啟用的
		);

	$page= isset($_GET['page'] )? $_GET['page']:'';
	if(!isset($pages[page])) redirect('?page=main') ; 
	$content = array(
			'username' => $_SESSION['admin_root'] ?'root' : 'admin',
			'title' => $pages[$page],
			'main' => '歡迎回來，請由左方進入各項功能', 
		);

	$is_post = $_SERVER['REQUEST_METHOD'] === 'POST' ;
	$db = database_get()';

	if(!$is_post) $_SESSION['admin_query'] = $_SERVER['QUERY_STRING'];

	switch ($page) {
		case 'main' :
			$apply_query = $db -> prepare('SELECT COUNT(*) AS `c`FROM `apply` WHERE `date` >=DATE(NOW()) AND `status` = 0 ') ;
			$apply_query -> execute();
			$apply_count = $apply_query->fetch();
		
			$seat_query = $db ->prepare('SELECT COUNT(*) AS `c` FROM' `seat`) ;
			$seat_query -> execute();
			$seat_count = $seat_query->fetch();

			$seat_active_query = $db ->prepare('SELECT COUNT(*) AS `c` FROM `seat` WHERE `disabled` = 0 ');
			$seat_active_query -> execute();
			$seat_active_count = $seat_active_query->fetch();

			$apply_month_query = $db ->prepare('SELECT COUNT(*) AS `c` FROM `apply` WHERE `date` >= :start AND `date` < :end');
			$apply_month_query ->bindValue(':start', date('Y-m-d',mktime( 0, 0, 0, date('m') ,1 )));
			$apply_month_query ->bindValue(':end' , date('y-m-d',mktime( 0, 0, 0, date('m') + 1 ,1)));
			$apply_month_query ->execute();
			$apply_month_count = $applt_month_query->fetch();

			$apply_month_accepted_query = $db ->prepare('SELECT COUNT(*) AS `c` FROM `apply` WHERE `date` >=:start AND `date` < :end AND `status` = 1 ' );
			$apply_month_accepted_query -> bindValue(':start', date('Y-m-d',mktime(0,0,0,date('m'),1)));
			$apply_month_accepted_query -> bindValue(':end' ,date('Y-m-d',mktime(0,0,0,date('m')+1,1)));
			$apply_month_accepted_query -> execute();
			$apply_month_accepted_count = $apply_month_accepted_query->fetch();

			$apply_history_query = $db ->prepare('SELECT COUNT(*) AS `c` FROM `apply`';
			$apply_history_query ->execute();
			$apply_history_count = $apply_history_query -> fetch();
			
			$apply_history_accepted_query = $db ->prepare('SELECT COUNT(*) AS `c` FROM `apply` WHERE `status` = 1 ');
			$apply_history_accepted_query ->execute();
			$apply_history_accepted_count = $apply_history_accepted_query->fetch();
			
			$w = array('一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二');
            $w = $w[(int) date('n') - 1];

            $content['main'] = '
                    <div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>' . $apply_count['c'] . '</h3>
                                    <p>待審核申請</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-ios-paper"></i>
                                </div>
                                <a href="?page=apply" class="small-box-footer">前往審核申請 <i class="fa fa-fw fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>' . $seat_active_count['c'] . '/' . $seat_count['c'] . '</h3>
                                    <p>座位啟用狀態</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-android-pin"></i>
                                </div>
                                <a href="?page=seat" class="small-box-footer">前往座位設定 <i class="fa fa-fw fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-teal">
                                <div class="inner">
                                    <h3>' . $apply_month_accepted_count['c'] . '/' . $apply_month_count['c'] . '</h3>
                                    <p>' . $w . '月申請與通過次數</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-ios-paper"></i>
                                </div>
                                <a href="?page=application" class="small-box-footer">前往申請記錄 <i class="fa fa-fw fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-light-blue">
                                <div class="inner">
                                    <h3>' . $apply_history_accepted_count['c'] . '/' . $apply_history_count['c'] . '</h3>
                                    <p>歷史申請與通過次數</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-ios-paper"></i>
                                </div>
                                <a href="?page=application" class="small-box-footer">前往申請記錄 <i class="fa fa-fw fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                ';
            break;
        case 'conflict':
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $json_str = json_encode(isset($_POST['pending']) ? get_pending_conflicts($id) : get_conflicts($id));
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Length: ' . $json_str);
            echo($json_str);
            exit();
        case 'config':
            $id = isset($_GET['id']) ? (int) $_GET['id'] : -1;
            if ($id > 0) {
                $seat_query = $db->prepare('SELECT `name` FROM `seat` WHERE `id` = :id');
                $seat_query->bindValue(':id', $id, PDO::PARAM_INT);
                $seat_query->execute();
                $seat = $seat_query->fetch();
            } else if ($id === 0) $seat = array('name' => '全部座位');
            if ($id < 0 || false === $seat) redirect('?page=seat');
            $action = isset($_GET['action']) ? $_GET['action'] : '';
            switch ($action) {
                case 'add':
                    if ($is_post) {
                        $okay = true;
                        $type       = isset($_POST['rule-type'])  ? (int) $_POST['rule-type'] : 0;
                        $date       = isset($_POST['date'])       ? $_POST['date'] : '';
                        $date_start = isset($_POST['date-start']) ? $_POST['date-start'] : '';
                        $date_end   = isset($_POST['date-end'])   ? $_POST['date-end'] : '';
                        $weekday =
                            (isset($_POST['weekday-0']) ?  1 : 0) +
                            (isset($_POST['weekday-1']) ?  2 : 0) +
                            (isset($_POST['weekday-2']) ?  4 : 0) +
                            (isset($_POST['weekday-3']) ?  8 : 0) +
                            (isset($_POST['weekday-4']) ? 16 : 0) +
                            (isset($_POST['weekday-5']) ? 32 : 0) +
                            (isset($_POST['weekday-6']) ? 64 : 0);
                        $time =
                            (isset($_POST['time-1'])  ?    1 : 0) ;
                        if ($type === 2 && $weekday === 127) {
                            $type = 1;
                            $weekday = 0;
                        }
                        if ($time === 0) $okay = false;
                        else switch ($type) {
                            case 0:
                                if (strtotime($date) === false) $okay = false;
                                else {
                                    $date_start = $date;
                                    $date_end   = null;
                                }
                                break;
                            case 2:
                                if ($weekday === 0) {
                                    $okay = false;
                                    break;
                                }
                            case 1:
                                if (strtotime($date_start) === false || strtotime($date_end) === false) $okay = false;
                                break;
                            default:
                                $okay = false;
                        }
                        if ($okay) {
                            $rule_insert = $db->prepare('INSERT INTO `seat_rules` (`seat`, `type`, `start`, `end`, `weekday`, `time1`,`ts`) VALUES (:seat, :type, :start, :end, :weekday, :time1, :ts)');
                            $rule_insert->bindValue(':seat', $id                     , PDO::PARAM_INT );
                            $rule_insert->bindValue(':type',      $type                   , PDO::PARAM_INT );
                            $rule_insert->bindValue(':start',     $date_start                              );
                            $rule_insert->bindValue(':end',       $date_end                                );
                            $rule_insert->bindValue(':weekday',   $weekday                , PDO::PARAM_INT );
                            $rule_insert->bindValue(':time1',     isset($_POST['time-1']) , PDO::PARAM_BOOL);
	                        $rule_insert->bindValue(':ts',        time()                  , PDO::PARAM_INT );
                            $rule_insert->execute();
                            redirect('?page=config&id=' . $id . '&action=list&add');
                        } else redirect('?page=config&id=' . $id . '&action=add' . ($okay ? '' : '&failed'));
                    }
                    $accepting_dates = array();
                    $apply_query = $db->prepare('SELECT * FROM `apply` WHERE `seat` = :seat AND `status` = 1 ORDER BY `date` ASC, `id` ASC');
                    $apply_query->bindValue(':seat', $id, PDO::PARAM_INT);
                    $apply_query->execute();
                    while (false !== ($apply = $apply_query->fetch())) {
                        if (!isset($accepting_dates[$apply['date']])) $accepting_dates[$apply['date']] = array();
                        $accepting_dates[$apply['date']][] = array(
                            'id'           => (int) $apply['id'],
                            'sid'          => $apply['sid'],
                            'date'         => $apply['date'],
                            'time'         => times_to_str($apply),
                            'times'        =>
                                ($apply['time1']  ?    1 : 0),
                            'organization' => $apply['organization'],
                            'applicant'    => $apply['applicant'],
                            'phone'        => $apply['phone'],
                        );
                    }
                    $content['main'] = '
                    <div id="modal-conflict" class="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="關閉"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">檢視衝突資訊</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="conflicts-container box box-warning">
                                        <div class="box-header"><h3 class="box-title">以下為該時段已通過之申請：</h3></div>
                                        <div class="box-body conflicts"></div>
                                        <div class="box-footer">以上已通過之申請將不會被取消，您確定要新增此規則嗎？</div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
                                    <button type="button" class="btn btn-warning" data-dismiss="modal">確定</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->' . (isset($_GET['failed']) ? '
                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4>新增規則失敗</h4>
                        <p>無法新增規則，請檢查是否填妥所有欄位。</p>
                    </div>' : '') . '
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-default">
                                <div class="box-body">
                                    <a class="btn btn-default" href="?page=seat">&laquo; 返回座位列表</a>
                                    <a class="btn btn-primary pull-right" href="?page=config&amp;id=' . $id . '&amp;action=list">檢視此座位所有規則 &raquo;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <form role="form" action="?page=config&amp;id=' . $id . '&amp;action=add" class="type-0" method="post">
                                <div class="box box-primary">
                                    <div class="box-header">
                                        <h3 class="box-title">新增不開放規則至「' . $seat['name'] . '」座位中</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="col-md-12 form-group fields field-rule-type">
                                            <label>規則類型</label>
                                            <div class="input-group btn-group" data-toggle="buttons">
                                                <label class="btn btn-default active">
                                                    <input type="radio" name="rule-type" value="0" checked />
                                                    <i class="fa fa-fw fa-calendar-o"></i>
                                                    單日不開放
                                                </label>
                                                <label class="btn btn-default">
                                                    <input type="radio" name="rule-type" value="1" />
                                                    <i class="fa fa-fw fa-calendar"></i>
                                                    連續不開放
                                                </label>
                                                //不開放設定
                                                /*<label class="btn btn-default">
                                                    <input type="radio" name="rule-type" value="2" />
                                                    <i class="fa fa-fw fa-calendar-check-o"></i>
                                                    依星期不開放
                                                </label> */
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group rule-type type-0 fields field-date">
                                            <label for="date">日期</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></div>
                                                <input id="date" name="date" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group rule-type type-1 type-2 fields field-date-start">
                                            <label for="date-start">開始日期</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></div>
                                                <input id="date-start" name="date-start" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group rule-type type-1 type-2 fields field-date-end">
                                            <label for="date-end">結束日期</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></div>
                                                <input id="date-end" name="date-end" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group rule-type type-2 fields field-weekdays">
                                            <label>選擇星期</label>
                                            <div class="input-group btn-group" data-toggle="buttons">
                                                <label class="btn btn-default">
                                                    <input type="checkbox" name="weekday-0" value="✓" /> 日
                                                </label>
                                                <label class="btn btn-default">
                                                    <input type="checkbox" name="weekday-1" value="✓" /> 一
                                                </label>
                                                <label class="btn btn-default">
                                                    <input type="checkbox" name="weekday-2" value="✓" /> 二
                                                </label>
                                                <label class="btn btn-default">
                                                    <input type="checkbox" name="weekday-3" value="✓" /> 三
                                                </label>
                                                <label class="btn btn-default">
                                                    <input type="checkbox" name="weekday-4" value="✓" /> 四
                                                </label>
                                                <label class="btn btn-default">
                                                    <input type="checkbox" name="weekday-5" value="✓" /> 五
                                                </label>
                                                <label class="btn btn-default">
                                                    <input type="checkbox" name="weekday-6" value="✓" /> 六
                                                </label>
                                                <a class="btn btn-info btn-select-none">全部取消</a>
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group rule-type type-0 type-1 type-2 fields field-times">
                                            <label>選擇時段</label>
                                            <div class="input-group btn-group" data-toggle="buttons">
                                                <label class="btn btn-default">
                                                    <input type="checkbox" name="time-1" value="✓" /> 1
                                                </label>
                                  
                                                <a class="btn btn-info btn-select-none">全部取消</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">新增規則</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <link rel="stylesheet" href="/datepicker/css/bootstrap-datepicker.min.css" />
                    <script src="/datepicker/js/bootstrap-datepicker.min.js"></script>
                    <script src="/datepicker/locales/bootstrap-datepicker.zh-TW.min.js"></script>
                    <script>
                        $(function() {
                            var getTimes = function() {
                                var fields = $(".field-times input:checked"), times = 0;
                                for (var i = 0; i < fields.length; ++i) {
                                    if (fields[i].checked) {
                                        switch (fields[i].name.substr(5)) {
                                            case "1":  times +=    1; break;
                                        }
                                    }
                                }
                                return times;
                            }, getWeekdays = function() {
                                var fields = $(".field-weekdays input:checked"), weekdays = 0;
                                for (var i = 0; i < fields.length; ++i) {
                                    if (fields[i].checked) {
                                        switch (fields[i].name.substr(8)) {
                                            case "0":  weekdays +=    1; break;
                                            case "1":  weekdays +=    2; break;
                                            case "2":  weekdays +=    4; break;
                                            case "3":  weekdays +=    8; break;
                                            case "4":  weekdays +=   16; break;
                                            case "5":  weekdays +=   32; break;
                                            case "6":  weekdays +=   64; break;
                                        }
                                    }
                                }
                                return weekdays;
                            }, accepting_dates = ' . json_encode($accepting_dates) . ';
                            $(".btn-select-none").click(function(event) {
                                event.stopPropagation();
                                $(this).parent().find("input").each(function() {
                                    if (this.checked) $(this).parent("label").button("toggle");
                                });
                            });
                            $("input[name=rule-type]").change(function() {
                                $(this).parents("form")[0].className = "type-" + this.value;
                            }).parents("form").submit(function(event) {
                                $(".fields").removeClass("has-error");
                                if (
                                    !this["rule-type"][0].checked &&
                                    !this["rule-type"][1].checked &&
                                    !this["rule-type"][2].checked
                                ) $(".field-rule-type").addClass("has-error");
                                else {
                                    var type = $("input[name=rule-type]:checked").val(),
                                        times = getTimes(), weekdays = getWeekdays();
                                    if (type === "0" && (this.date.value          === "" || !moment(this.date.value         ).isValid())) $(".field-date"      ).addClass("has-error");
                                    if (type !== "0" && (this["date-start"].value === "" || !moment(this["date-start"].value).isValid())) $(".field-date-start").addClass("has-error");
                                    if (type !== "0" && (this["date-end"  ].value === "" || !moment(this["date-end"  ].value).isValid())) $(".field-date-end"  ).addClass("has-error");
                                    if (type === "2" && weekdays === 0) $(".field-weekdays").addClass("has-error");
                                    if (times === 0) $(".field-times").addClass("has-error");
                                    if (type !== "0" &&
                                        !$(".has-error.field-date-start").length &&
                                        !$(".has-error.field-date-end"  ).length &&
                                        moment(this["date-end"].value).diff(moment(this["date-start"].value), "days") <= 0
                                    ) $(".field-date-end").addClass("has-error");
                                }
                                if (!$(".has-error").length) {
                                    if (!$(this).hasClass("conflicts-confirmed")) {
                                        var d = (type === "0") ? moment(this["date"].value) : moment(this["date-start"].value),
                                            e = (type === "0") ? moment(this["date"].value) : moment(this["date-end"  ].value),
                                            html = "";
                                        while (d.diff(e, "days") <= 0) {
                                            if (type !== "2" || (Math.pow(2, d.day()) & weekdays) > 0) {
                                                var date = d.format("YYYY-MM-DD");
                                                if (accepting_dates[date] && accepting_dates[date].length) {
                                                    for (var i = 0; i < accepting_dates[date].length; ++i) {
                                                        if ((accepting_dates[date][i].times & times) === 0) continue;
                                                        html += "<div class=\\"box box-primary\\"><div class=\\"box-body\\">";
                                                        html += "<p>學號："     + accepting_dates[date][i].sid                                     + "</p>";
                                                        html += "<p>座位：'     . $seat['name']                                               . '</p>";
                                                        html += "<p>日期："     + moment(accepting_dates[date][i].date).format("YYYY-MM-DD（dd）") + "</p>";
                                                        html += "<p>時段："     + accepting_dates[date][i].time                                    + "</p>";
                                                        html += "<p>申請系所：" + accepting_dates[date][i].organization                            + "</p>";
                                                        html += "<p>申請人："   + accepting_dates[date][i].applicant                               + "</p>";
                                                        html += "<p>聯絡電話：<a href=\"tel:" + accepting_dates[date][i].phone + "\">" + accepting_dates[date][i].phone + "</a></p>";
                                                        html += "</div></div>";
                                                    }
                                                }
                                            }
                                            d = d.add(1, "days");
                                        }
                                        if (html !== "") {
                                            event.preventDefault();
                                            $("#modal-conflict .conflicts").html(html);
                                            $("#modal-conflict").modal("show");
                                        }
                                    }
                                } else event.preventDefault();
                            });
                            $("#modal-conflict .btn-default").click(function() {
                                $("form").removeClass("conflicts-confirmed");
                            });
                            $("#modal-conflict .btn-warning").click(function() {
                                $("form").addClass("conflicts-confirmed").submit();
                            });
                            $("#date,#date-start,#date-end").datepicker({
                                format: "yyyy-mm-dd",
                                weekStart: 0,
                                todayBtn: "linked",
                                language: "zh-TW",
                                beforeShowDay: function (date) {
                                    return { classes: accepting_dates[moment(date).format("YYYY-MM-DD")] ? "date-accepting" : "" };
                                }
                            }).datepicker("setDate", moment().format("YYYY-MM-DD")).on("changeDate.datepicker", function(event) {
                                $(this).datepicker("hide");
                            });
                        });
                    </script>
                    <style>
                        form .rule-type {
                            display: none;
                        }
                        form.type-0 .rule-type.type-0,
                        form.type-1 .rule-type.type-1,
                        form.type-2 .rule-type.type-2 {
                            display: block;
                        }
                        .date-accepting,
                        .datepicker table tr td.active.date-accepting {
                            color: red;
                            font-weight: bold;
                        }
                    </style>
';
                    break;
                case 'list':
                    if ($is_post) {
                        $delete = isset($_POST['id']) ? (int) $_POST['id'] : 0;
                        $delete_query = $db->prepare("DELETE FROM `seat_rules` WHERE `id` = :id LIMIT 1");
                        $delete_query->bindValue(':id', $delete, PDO::PARAM_INT);
                        $delete_query->execute();
                        redirect('?page=config&id=' . $id . '&action=list&deleted');
                    }
                    if (isset($_GET['deleteAll'])) {
                        $delete_query = $db->prepare("DELETE FROM `seat_rules` WHERE `seat` = :seat");
                        $delete_query->bindValue(':id', $id, PDO::PARAM_INT);
                        $delete_query->execute();
                        redirect('?page=config&id=' . $id . '&action=list&deletedAll');
                    }
                    $content['main'] = '' . (isset($_GET['add']) ? '
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4>已新增規則</h4>
                        <p>您輸入的規則已新增至規則列表中，若要調整該規則的設定，請刪除該規則後再次新增新的規則。</p>
                    </div>' : '') . (isset($_GET['deleted']) ? '
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4>已刪除規則</h4>
                        <p>您所選擇的規則已成功從規則列表中刪除，若要新增其他規則，請點選「<a href="?page=config&amp;id=' . $id . '&amp;action=add">新增規則至此座位中</a>」按鈕。</p>
                    </div>' : '') . (isset($_GET['deletedAll']) ? '
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4>已刪除該座位所有規則</h4>
                        <p>該座位之所有規則已成功從規則列表中刪除，若要新增規則，請點選「<a href="?page=config&amp;id=' . $id . '&amp;action=add">新增規則至此座位中</a>」按鈕。</p>
                    </div>' : '') . '
                    <div id="modal" class="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="關閉"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">您確定要刪除此規則嗎？</h4>
                                </div>
                                <div class="modal-body">
                                    <p>座位：' . $seat['name'] . '</p>
                                    <p>類型：<span></span></p>
                                    <p>日期：<span></span></p>
                                    <p>星期：<span></span></p>
                                    <p>時段：<span></span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>                                    
                                    <form id="delete-rule" action="?page=config&amp;id=' . $id . '&amp;action=list" method="post">
                                        <input type="hidden" name="id" />
                                        <button type="submit" class="btn btn-danger">刪除</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-default">
                                <div class="box-body">
                                    <a class="btn btn-default" href="?page=seat">&laquo; 返回座位列表</a>
                                    <a class="btn btn-primary pull-right" href="?page=config&amp;id=' . $id . '&amp;action=add">新增規則至此座位中 &raquo;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">以下為所有「' . $seat['name'] . '」座位的不開放規則</h3>
                                    <a data-toggle="confirmation" data-popout="true" data-title="您確定要刪除此座位之所有規則嗎？" data-btn-ok-label="全部刪除" data-btn-ok-icon="fa fa-fw fa-trash" data-btn-ok-class="btn-danger" data-btn-cancel-label="取消" data-btn-cancel-icon="fa fa-fw fa-times" data-btn-cancel-class="btn-default" href="?page=config&amp;id=' . $id . '&amp;action=list&amp;deleteAll" class="btn btn-sm btn-danger pull-right btn-delete-all">
                                        <i class="fa fa-fw fa-trash"></i>
                                        刪除此座位之所有規則
                                    </a>
                                </div>
                                <div class="box-body">
                                    <table id="rules" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="hidden-xs">ID</th>
                                                <th><span class="hidden-xs">規則</span>類型</th>
                                                <th>日期</th>
                                                <th>星期</th>
                                                <th>時段</th>
                                                <th>刪除</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                        </tfoot>
                                        <tbody>';
                    $rule_query = $db->prepare("SELECT * FROM `seat_rules` WHERE `seat` = :seat ORDER BY `end` DESC, `start` DESC, `id` DESC");
                    $rule_query->bindValue(':seat', $id, PDO::PARAM_INT);
                    $rule_query->execute();
                    while (false !== ($rule = $rule_query->fetch())) {
                        $date_start = strtotime($rule['start']);
                        if ($rule['type'] !== '0') $date_end = strtotime($rule['end']);
                        $w = array('日', '一', '二', '三', '四', '五', '六');
                        $date = date('Y-m-d', $date_start) . '（' . $w[(int) date('w', $date_start)] . '）' . ($rule['type'] === '0' ? '' : '～' . date('Y-m-d', $date_end) . '（' . $w[(int) date('w', $date_end)] . '）');
                        $weekdays = '-';
                        if ($rule['type'] !== '0') {
                            $weekdays = times_to_str($rule['weekday'], '、', '～', array(1 => '日', 2 => '一', 4 => '二', 8 => '三', 16 => '四', 32 => '五', 64 => '六'), false);
                            if (empty($weekdays)) $weekdays = '-';
                        }
                        $content['main'] .= '
                                            <tr id="row-' . $rule['id'] . '">
                                                <td class="column-id hidden-xs">' . $rule['id'] . '</td>
                                                <td class="column-type"><i class="fa fa-fw fa-' . ($rule['type'] === '2' ? 'calendar-check-o' : ($rule['type'] === '1' ? 'calendar' : 'calendar-o')) . '"></i><span class="hidden-xs"> ' . ($rule['type'] === '2' ? '依星期' : ($rule['type'] === '1' ? '連續' : '單日')) . '<span class="hidden-sm">不開放</span></span></td>
                                                <td class="column-date">' . $date . '</td>
                                                <td class="column-weekday">' . $weekdays . '</td>
                                                <td class="column-time">' . times_to_str($rule) . '</td>
                                                <td class="column-delete"><a class="btn btn-xs btn-danger" href="javascript:deleteRule(\'' . $rule['id'] . '\');" title="刪除"><i class="fa fa-fw fa-trash"></i> 刪除</a></td>
                                            </tr>';
                    }
                    $content['main'] .= '
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        $(function() {
                            $("[data-toggle=confirmation]").confirmation();
                            $("#rules").DataTable({ lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "顯示全部"]], language: {
                                emptyTable:     "無資料可供顯示。",
                                info:           "正在顯示第 _START_ 至 _END_ 筆資料，共 _TOTAL_ 筆",
                                infoEmpty:      "無資料可供顯示。",
                                infoFiltered:   "（從 _MAX_ 筆資料中篩選）",
                                infoPostFix:    "",
                                lengthMenu:     "每頁顯示 _MENU_ 筆資料",
                                loadingRecords: "載入中…",
                                processing:     "處理中…",
                                search:         "篩選結果：",
                                zeroRecords:    "找不到符合的結果。",
                                paginate: { sPrevious: "&laquo; 上一頁", sNext: "下一頁 &raquo;" }
                            }, order: [[ 2, "desc" ], [ 0, "desc" ]] });
                            if ($(".dataTables_empty").length) $(".btn-delete-all").hide();
                            window.deleteRule = function(id) {
                                var row = $("#row-" + id), columns = $("#modal .modal-body p > span");
                                columns[0].innerHTML   = row.find(".column-type").html();
                                columns[1].textContent = row.find(".column-date").text();
                                columns[2].textContent = row.find(".column-weekday").text();
                                columns[3].textContent = row.find(".column-time").text();
                                $("#delete-rule input[name=id]").val(id);
                                $(columns[0]).find(".hidden-xs").removeClass("hidden-xs");
                                $(columns[0]).find(".hidden-sm").removeClass("hidden-sm");
                                $("#modal").modal("show");
                            };
                        });
                    </script>
';
                    break;
                default:
                    redirect('?page=config&id=' . $id . '&action=list');
            }
            break;
        case 'apply':
        case 'application':
        case 'seat':
            if (isset($_POST['data'])) {
                $data = json_decode($_POST['data'], true);
                if ($page === 'apply') {
                    if (check_acceptable($data['id'])) {
                        $apply_update = $db->prepare('UPDATE `apply` SET `status` = 1 WHERE `id` = :id AND `status` = 0');
                        $apply_update->bindValue(':id', (int) $data['id'], PDO::PARAM_INT);
                        $apply_update->execute();
                    }
                } else if ($page === 'seat') {
                    $seat_update = $db->prepare('UPDATE `seat` SET `disabled` = :state WHERE `id` = :id AND `disabled` = :nstate');
                    $seat_update->bindValue(':id', (int) $data['id'], PDO::PARAM_INT);
                    $seat_update->bindValue(':nstate', $data['state'], PDO::PARAM_BOOL);
                    $seat_update->bindValue(':state', !$data['state'], PDO::PARAM_BOOL);
                    $seat_update->execute();
                }
                header('Content-Type: application/json; charset=utf-8');
                header('Content-Length: 4');
                echo('true');
                exit();
            } else if ($page === 'apply') {
                if (isset($_POST['reject'])) {
                    $apply_update = $db->prepare('UPDATE `apply` SET `status` = 4 WHERE `id` = :id AND `status` = 0');
                    $apply_update->bindValue(':id', (int) $_POST['reject'], PDO::PARAM_INT);
                    $apply_update->execute();
                    header('Content-Type: application/json; charset=utf-8');
                    header('Content-Length: 4');
                    echo('true');
                    exit();
                }
            } else if ($page === 'seat') {
                if (isset($_POST['id']) && isset($_POST['save'])) {
                    $seat_update = $db->prepare('UPDATE `seat` SET `name` = :name WHERE `id` = :id');
                    $seat_update->bindValue(':id', (int) $_POST['id'], PDO::PARAM_INT);
                    $seat_update->bindValue(':name', $_POST['save']);
                    $seat_update->execute();
                    header('Content-Type: application/json; charset=utf-8');
                    header('Content-Length: 4');
                    echo('true');
                    exit();
                } else if (isset($_POST['name'])) {
                    $seat_update = $db->prepare('INSERT INTO `seat` (`name`) VALUES (:name)');
                    $seat_update->bindValue(':name', $_POST['name']);
                    $seat_update->execute();
                    redirect('?page=seat&add');
                }
            } else if ($page === 'application') {
                $year = (int) (isset($_GET['year']) ? $_GET['year'] : date('Y'));
                $month = (int) (isset($_GET['month']) ? $_GET['month'] : date('m'));
                $application_months_query = $db->prepare('SELECT DATE_FORMAT(`date`, "%Y-%m-01") AS `months`, COUNT(*) AS `count` FROM `apply` GROUP BY `months` ORDER BY `months` DESC');
                $application_months_query->execute();
                $application_period = '';
                while (false !== ($application_months = $application_months_query->fetch())) {
                    $date = $application_months['months'];
                    $dates = explode('-', $date, 3);
                    $application_period .= "<option value=\"$dates[0]_$dates[1]\"" . (date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)) === $date ? ' selected="selected"' : '') . ">$dates[0] 年 $dates[1] 月（共 $application_months[count] 筆）</option>";
                }
            }
            $display_columns = $page !== 'seat' ? array(
                'id'           => array('name' => 'ID',       'class' => ' hidden-xs hidden-sm hidden-md hidden-lg'),
                'sid'          => array('name' => '學號',     'class' => ''),
                'seat'    => array('name' => '座位',     'class' => ''),
                'date'         => array('name' => '日期',     'class' => ''),
                'time'         => array('name' => '時段',     'class' => ' hidden-xs hidden-sm hidden-md'),
                'organization' => array('name' => '申請系所', 'class' => ' hidden-xs hidden-sm'),
                'applicant'    => array('name' => '申請人',   'class' => ' hidden-xs hidden-sm'),
                'phone'        => array('name' => '聯絡電話', 'class' => ' hidden-xs hidden-sm hidden-md'),
                'status'       => array('name' => '狀態',     'class' => ''),
            ) : array(
                'id'       => array('name' => 'ID',       'class' => ' hidden-xs'),
                'name'     => array('name' => '座位名稱', 'class' => ''),
                'disabled' => array('name' => '狀態',     'class' => ' hidden-xs'),
                'toggle'   => array('name' => '切換',     'class' => ''),
                'config'   => array('name' => '設定',     'class' => ''),
            );
            if ($page !== 'apply') unset($display_columns['select']);
            if ($page === 'apply') $display_columns['accepting'] = array('name' => '審核', 'class' => '');
            if ($page !== 'seat') {
                $seat_query = $db->prepare('SELECT `id`, `name` FROM `seat`');
                $seat_query->execute();
                $seat = array();
                while (false !== ($row = $seat_query->fetch()))
                    $seat[$row['id']] = $row['name'];
            }
            $dataset_query = $db->prepare('SELECT * FROM `' . ($page !== 'seat' ? 'apply' : 'seat') . '`' . ($page === 'apply' ? ' WHERE `date` >= DATE(NOW()) AND `status` = 0' : '') . ($page === 'application' ? ' WHERE `date` >= :start AND `date` <= :end' : '') . ($page !== 'seat' ? ' ORDER BY `date` DESC' : ''));
            if ($page === 'application') {
                $dataset_query->bindValue(':start', date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)));
                $dataset_query->bindValue(':end', date('Y-m-d', mktime(0, 0, 0, $month + 1, -1, $year)));
            }
            $dataset_query->execute();
            $dataset = $dataset_query->fetchAll();
            $html = ($page === 'seat' && isset($_GET['add']) ? '
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4>已新增座位</h4>
                        <p>您輸入的座位已新增至座位列表中，若要調整該座位的設定，請點選該座位的設定按鈕。</p>
                    </div>' : '') . ($page !== 'application' ? ('
                    <div id="modal" class="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="關閉"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">' . ($page === 'apply' ? '您確定要通過此申請之審核嗎？' : '您確定要切換狀態嗎？') . '</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="box box-primary">
                                        <div class="box-body">' . ($page === 'apply' ? '
                                            <p>學號：<span></span></p>
                                            <p>座位：<span></span></p>
                                            <p>日期：<span></span></p>
                                            <p>時段：<span></span></p>
                                            <p>申請系所：<span></span></p>
                                            <p>申請人：<span></span></p>
                                            <p>聯絡電話：<span></span></p>' : '') . ($page === 'seat' ? '
                                            <p>座位名稱：<span></span></p>
                                            <p>目前狀態：<span></span></p>
                                            <p>您將切換座位狀態為<span></span>！</p>' : '') . '
                                        </div>
                                    </div>' . ($page === 'apply' ? '
                                    <div class="conflicts-container box box-warning">
                                        <div class="box-header"><h3 class="box-title">以下為該時段之其他申請：</h3></div>
                                        <div class="box-body conflicts"></div>
                                    </div>' : '') . '
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
                                    <button type="button" class="btn btn-primary">確定</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->') : '') . ($page === 'application' ? '
                    <div id="modal-detail" class="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="關閉"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">檢視詳細資料</h4>
                                </div>	
                                <div class="modal-body">
                                    <p>學號：<span></span></p>
                                    <p>座位：<span></span></p>
                                    <p>日期：<span></span></p>
                                    <p>時段：<span></span></p>
                                    <p>申請系所：<span></span></p>
                                    <p>申請人：<span></span></p>
                                    <p>聯絡電話：<span></span></p>
                                    <p>目前狀態：<span></span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->' : '') . ($page === 'apply' ? '
                    <div id="modal-conflict" class="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="關閉"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">檢視衝突資訊</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="conflicts-container box box-warning">
                                        <div class="box-header"><h3 class="box-title">以下為該時段已通過之申請：</h3></div>
                                        <div class="box-body conflicts"></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <div id="modal-reject" class="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="關閉"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">您確定要駁回此申請之審核嗎？</h4>
                                </div>
                                <div class="modal-body">
                                    <p>學號：<span></span></p>
                                    <p>座位：<span></span></p>
                                    <p>日期：<span></span></p>
                                    <p>時段：<span></span></p>
                                    <p>申請系所：<span></span></p>
                                    <p>申請人：<span></span></p>
                                    <p>聯絡電話：<span></span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
                                    <button type="button" class="btn btn-danger">駁回</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->' : '') . '
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">檢視' . ($page === 'application' ? '' : ('與' . ($page === 'apply' ? '' : '修改'))) . $pages[$page] . ($page === 'application' ? "（$year 年 $month 月）" : '') . '</h3>' . ($page === 'application' ? '
                                    <div class="form-group pull-right">
                                        <label>選擇檢視月份</label>
                                        <select id="application_period" class="form-control">' . $application_period . '</select>
                                    </div>' : '') . ($page === 'apply' ? '
                                    <button data-toggle="confirmation" data-popout="true" data-title="您確定要通過所有已勾選之申請嗎？" data-btn-ok-label="全部通過" data-btn-ok-icon="fa fa-fw fa-check-square-o" data-btn-ok-class="btn-success" data-btn-cancel-label="取消" data-btn-cancel-icon="fa fa-fw fa-times" data-btn-cancel-class="btn-default" class="btn btn-sm btn-success pull-right btn-selection-accepting hide">
                                        <i class="fa fa-fw fa-check-square-o"></i>
                                        通過所有已勾選之 <span></span> 個申請
                                    </button>' : '') . ($page === 'seat' ? '
                                    <a class="btn btn-sm btn-default pull-right" href="?page=config&amp;id=0" title="全部座位詳細設定">
                                        <i class="fa fa-fw fa-sliders"></i>
                                        <span class="hidden-xs"> 全部座位詳細設定</span>
                                    </a>' : '') . '
                                </div><!-- /.box-header -->
                                <div class="box-body">' . ($page === 'seat' ? '
                                    <form role="form" action="?page=seat" method="post">
                                        <div class="form-group">
                                            <label for="place-name">新增座位</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-fw fa-pencil"></i></span>
                                                <input id="place-name" name="name" class="form-control" placeholder="請輸入座位名稱" />
                                                <div class="input-group-btn">
                                                    <button id="place-add" type="submit" class="btn btn-primary">新增座位</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>' : '') . '
                                    <table id="dataset" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>';
            foreach ($display_columns as $column) {
                $html .= '
                                                <th class="' . $column['class'] . '" title="' . $column['name'] . '">' . $column['name'] . '</th>';
            }
            $html .= '
                                            </tr>
                                        </thead>
                                        <tbody>';
            foreach ($dataset as $data) {
                $html .= '
                                            <tr id="row-' . $data['id'] . '">';
                foreach ($display_columns as $column_name => $column) {
                    switch ($column_name) {
                        case 'seat':
                            $column['value'] = isset($seat[$data['seat']]) ? $seat[$data['seat']] : '（無此座位）';
                            break;
                        case 'date':
                            $column_date = strtotime($data['date']);
                            $w = array('日', '一', '二', '三', '四', '五', '六');
                            $column['value'] = date('Y-m-d', $column_date) . '（' . $w[(int) date('w', $column_date)] . '）';
                            break;
                        case 'time':
                            $column['value'] = times_to_str($data);
                            if (empty($column['value'])) $column['value'] = '（無時段）';
                            if ($column['value'] === '1~D') $column['value'] = '整天';
                            break;
                        case 'phone':
                            $column['value'] = '<a href="tel:' . $data['phone'] . '">' . $data['phone'] . '</a>';
                            break;
                        case 'status':
                            $status = (int) $data['status'];
                            $past = strtotime($data['date']) < mktime(0, 0, 0);
                            $column['class'] .= ' label-' . ($status === 4 ? 'danger' : ($status > 1 ? 'default' : ($status === 1 ? 'success' : ($past ? 'warning' : 'primary'))));
                            $column['value'] = '<i class="fa fa-fw fa-' . ($status === 4 ? 'times' : ($status > 1 ? 'trash' : ($status === 1 ? 'check' : ($past ? 'clock-o' : 'hourglass-start')))) . '"></i><span class="hidden-xs hidden-sm hidden-md"> ' . ($status === 4 ? '已駁回' : ($status > 1 ? '已取消' : ($status === 1 ? '已通過' : ($past ? '已過期' : '審核中')))) . '</span>';
                            if ($page === 'application') $column['value'] .= '<a class="btn btn-xs btn-primary hidden-lg pull-right" href="javascript:dashboardDetail(\'' . $data['id'] . '\');" title="檢視詳細資料"><i class="fa fa-fw fa-file-text-o"></i></a>';
                            break;
                        case 'accepting':
                            $status = (int) $data['status'];
                            $column['class'] .= ' label-default';
                            $column['value'] = $status === 0 ? ((check_acceptable($data['id']) ? ('<a class="btn btn-xs btn-success" href="javascript:dashboardAccpet(\'' . $data['id'] . '\');" title="通過"><i class="fa fa-fw fa-check-square-o"></i><span class="hidden-xs"> 通過</span></a>') : ('<a class="btn btn-xs btn-warning" href="javascript:dashboardConflict(\'' . $data['id'] . '\');" title="檢視衝突資訊"><i class="fa fa-fw fa-exclamation-triangle"></i><span class="hidden-xs"> 衝突</span></a>')) . ' <a class="btn btn-xs btn-danger" href="javascript:dashboardReject(\'' . $data['id'] . '\');" title="駁回"><i class="fa fa-fw fa-times"></i><span class="hidden-xs"> 駁回</span></a>') : '';
                            break;
                        case 'name':
                            $column['value'] = '
                                                    <span id="place-name-display-' . $data['id'] . '" class="place-name-display">' . $data[$column_name] . '</span>
                                                    <div id="place-name-edit-' . $data['id'] . '" class="input-group place-name-edit hide">
                                                        <span class="input-group-addon hidden-xs hidden-sm"><i class="fa fa-fw fa-pencil"></i></span>
                                                        <input class="form-control place-name" placeholder="請輸入座位名稱" />
                                                        <div class="input-group-btn">
                                                            <a title="儲存座位名稱" id="place-name-save-' . $data['id'] . '" href="javascript:dashboardSave(\'' . $data['id'] . '\');" class="btn btn-primary place-name-save"><i class="fa fa-fw fa-check hidden-md hidden-lg"></i><span class="hidden-xs hidden-sm">儲存</span></a>
                                                            <a title="取消" id="place-name-cancel-' . $data['id'] . '" href="javascript:dashboardCancel(\'' . $data['id'] . '\');" class="btn btn-danger place-name-cancel"><i class="fa fa-fw fa-times hidden-md hidden-lg"></i><span class="hidden-xs hidden-sm">取消</span></a>
                                                        </div>
                                                    </div>
                                                    <a id="place-name-edit-button-' . $data['id'] . '" class="btn btn-sm btn-primary pull-right place-name-edit-button" href="javascript:dashboardEdit(\'' . $data['id'] . '\');" title="修改座位名稱"><i class="fa fa-fw fa-pencil"></i><span class="hidden-xs"> 修改</span></a>
                                                ';
                            break;
                        case 'disabled':
                            $disabled = $data['disabled'] ? true : false;
                            $column['class'] .= ' label-' . ($disabled ? 'default' : 'primary');
                            $column['value'] = '<i class="fa fa-fw fa-eye' . ($disabled ? '-slash' : '') . '"></i><span class="hidden-xs"> ' . ($disabled ? '已停用' : '已啟用') . '</span>';
                            break;
                        case 'toggle':
                            $disabled = $data['disabled'] ? true : false;
                            $column['class'] .= ' label-' . ($disabled ? 'default' : 'primary');
                            $column['value'] = '<a class="btn btn-sm btn-default" href="javascript:dashboard' . ($disabled ? 'Enable' : 'Disable') . '(\'' . $data['id'] . '\');" title="' . ($disabled ? '啟用' : '停用') . '"><i class="fa fa-fw fa-toggle-' . ($disabled ? 'off' : 'on') . '"></i><span class="hidden-xs"> ' . ($disabled ? '啟用' : '停用') . '</span></a>';
                            break;
                        case 'config':
                            $column['value'] = '<a class="btn btn-sm btn-default" href="?page=config&id=' . $data['id'] . '" title="座位詳細設定"><i class="fa fa-fw fa-sliders"></i><span class="hidden-xs"> 詳細設定</span></a>';
                            break;
                        default:
                            $column['value'] = $data[$column_name];
                            break;
                    }
                    $html .= '
                                                <td class="column-' . $column_name . $column['class'] . '" title="' . $column['name'] . '">' . $column['value'] . '</td>';
                }
                $html .= '
                                            </tr>';
            }
            $html .= '
                                        </tbody>
                                        <tfoot>
                                            <tr>';
            foreach ($display_columns as $column) {
                $html .= '
                                                <th class="' . $column['class'] . '" title="' . $column['name'] . '">' . $column['name'] . '</th>';
            }
            $html .= '
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            <style>#dataset .btn{display:inline}</style>
                            <script src="//cdn.datatables.net/plug-ins/1.10.11/api/fnAddDataAndDisplay.js"></script>
                            <script>
                                $(function() {
                                    $("#dataset").DataTable({ lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "顯示全部"]], language: {
                                        emptyTable:     "無資料可供顯示。",
                                        info:           "正在顯示第 _START_ 至 _END_ 筆資料，共 _TOTAL_ 筆",
                                        infoEmpty:      "無資料可供顯示。",
                                        infoFiltered:   "（從 _MAX_ 筆資料中篩選）",
                                        infoPostFix:    "",
                                        lengthMenu:     "每頁顯示 _MENU_ 筆資料",
                                        loadingRecords: "載入中…",
                                        processing:     "處理中…",
                                        search:         "篩選結果：",
                                        zeroRecords:    "找不到符合的結果。",
                                        paginate: { sPrevious: "&laquo; 上一頁", sNext: "下一頁 &raquo;" }
                                    }, order: [' . ($page !== 'seat' ? ('[ 3, "' . ($page === 'apply' ? 'asc' : 'desc') . '" ], ') : '') . '[ 0, "asc" ]] });
                                    var postingData = {}, postingCallback = function() {' . ($page === 'apply' ? '
                                        $("#row-" + postingData.id).find(".column-status").removeClass("label-primary").addClass("label-success").html("<i class=\\"fa fa-fw fa-check\\"></i><span class=\\"hidden-xs\\"> 已通過</span>");
                                        $("#row-" + postingData.id).find(".column-accepting a").hide();' : '')  . ($page === 'seat' ? '
                                        var a = postingData.state ? "label-default" : "label-primary",
                                            b = postingData.state ? "label-primary" : "label-default",
                                            c = postingData.state ? "<i class=\\"fa fa-fw fa-eye\\"></i><span class=\\"hidden-xs\\"> 已啟用</span>" : "<i class=\\"fa fa-fw fa-eye-slash\\"></i><span class=\\"hidden-xs\\"> 已停用</span>",
                                            d = postingData.state ? " 停用" : " 啟用",
                                            e = "javascript:" + (postingData.state ? "dashboardDisable" : "dashboardEnable") + "(" + postingData.id + ");",
                                            f = postingData.state ? "fa-toggle-off" : "fa-toggle-on",
                                            g = postingData.state ? "fa-toggle-on" : "fa-toggle-off";
                                        $("#row-" + postingData.id).find(".column-disabled").removeClass(a).addClass(b).html(c);
                                        h = $("#row-" + postingData.id).find(".column-toggle").removeClass(a).addClass(b).find("a").prop("href", e);
                                        h.find("i").removeClass(f).addClass(g);
                                        h.find("span").text(d);' : '') . '
                                        $("#modal").modal("hide");
                                    };' . ($page === 'application' ? '
                                    $("#application_period").change(function() {
                                        var date = $(this).val().split("_", 2);
                                        location = "?page=application&year=" + date[0] + "&month=" + date[1];
                                    });
                                    window.dashboardDetail = function(id) {
                                        var row = $("#row-" + id), columns = $("#modal-detail .modal-body span");
                                        columns[0].textContent = row.find(".column-sid").text();
                                        columns[1].textContent = row.find(".column-seat").text();
                                        columns[2].textContent = row.find(".column-date").text();
                                        columns[3].textContent = row.find(".column-time").text();
                                        columns[4].textContent = row.find(".column-organization").text();
                                        columns[5].textContent = row.find(".column-applicant").text();
                                        columns[6].innerHTML   = "<a href=\"tel:" + row.find(".column-phone").text() + "\">" + row.find(".column-phone").text() + "</a>";
                                        columns[7].innerHTML   = row.find(".column-status").html();
                                        $(columns[7]).find(".hidden-xs").removeClass("hidden-xs");
                                        $(columns[7]).find(".hidden-sm").removeClass("hidden-sm");
                                        $(columns[7]).find(".hidden-md").removeClass("hidden-md");
                                        $(columns[7]).find("a").hide();
                                        $("#modal-detail").modal("show");
                                    };' : '') . ($page === 'apply' ? '
                                    window.dashboardAccpet = function(id) {
                                        postingData.path = "?page=apply";
                                        postingData.id = id;
                                        var row = $("#row-" + id), columns = $("#modal .modal-body span");
                                        columns[0].textContent = row.find(".column-sid").text();
                                        columns[1].textContent = row.find(".column-seat").text();
                                        columns[2].textContent = row.find(".column-date").text();
                                        columns[3].textContent = row.find(".column-time").text();
                                        columns[4].textContent = row.find(".column-organization").text();
                                        columns[5].textContent = row.find(".column-applicant").text();
                                        columns[6].innerHTML   = "<a href=\"tel:" + row.find(".column-phone").text() + "\">" + row.find(".column-phone").text() + "</a>";
                                        $.ajax({
                                            url: "?page=conflict",
                                            method: "POST",
                                            data: { id: id, pending: true },
                                            dataType: "json",
                                            async: true,
                                            success: function(conflicts) {
                                                var html = "";
                                                for (var i = 0; i < conflicts.length; ++i) {
                                                    html += "<div class=\\"box box-primary\\"><div class=\\"box-body\\">";
                                                    html += "<p>學號："     + conflicts[i].sid                                     + "</p>";
                                                    html += "<p>座位："     + conflicts[i].place                                   + "</p>";
                                                    html += "<p>日期："     + moment(conflicts[i].date).format("YYYY-MM-DD（dd）") + "</p>";
                                                    html += "<p>時段："     + conflicts[i].time                                    + "</p>";
                                                    html += "<p>申請系所：" + conflicts[i].organization                            + "</p>";
                                                    html += "<p>申請人："   + conflicts[i].applicant                               + "</p>";
                                                    html += "<p>聯絡電話：<a href=\"tel:" + conflicts[i].phone + "\">" + conflicts[i].phone + "</a></p>";
                                                    html += "</div></div>";
                                                }
                                                if (html !== "") {
                                                    $("#modal .modal-body .conflicts").html(html);
                                                    $("#modal .modal-body .conflicts-container").removeClass("hide");
                                                } else {
                                                    $("#modal .modal-body .conflicts-container").addClass("hide");
                                                }
                                                $("#modal").modal("show");
                                            }
                                        });
                                    };
                                    window.dashboardConflict = function(id) {
                                        $.ajax({
                                            url: "?page=conflict",
                                            method: "POST",
                                            data: { id: id },
                                            dataType: "json",
                                            async: true,
                                            success: function(conflicts) {
                                                var html = "";
                                                for (var i = 0; i < conflicts.length; ++i) {
                                                    html += "<div class=\\"box box-primary\\"><div class=\\"box-body\\">";
                                                    html += "<p>學號："     + conflicts[i].sid                                     + "</p>";
                                                    html += "<p>座位："     + conflicts[i].place                                   + "</p>";
                                                    html += "<p>日期："     + moment(conflicts[i].date).format("YYYY-MM-DD（dd）") + "</p>";
                                                    html += "<p>時段："     + conflicts[i].time                                    + "</p>";
                                                    html += "<p>申請系所：" + conflicts[i].organization                            + "</p>";
                                                    html += "<p>申請人："   + conflicts[i].applicant                               + "</p>";
                                                    html += "<p>聯絡電話：<a href=\"tel:" + conflicts[i].phone + "\">" + conflicts[i].phone + "</a></p>";
                                                    html += "</div></div>";
                                                }
                                                $("#modal-conflict .modal-body .conflicts").html(html);
                                                $("#modal-conflict").modal("show");
                                            }
                                        });
                                    };
                                    window.dashboardReject = function(id) {
                                        postingData.id = id;
                                        var row = $("#row-" + id), columns = $("#modal-reject .modal-body span");
                                        columns[0].textContent = row.find(".column-sid").text();
                                        columns[1].textContent = row.find(".column-seat").text();
                                        columns[2].textContent = row.find(".column-date").text();
                                        columns[3].textContent = row.find(".column-time").text();
                                        columns[4].textContent = row.find(".column-organization").text();
                                        columns[5].textContent = row.find(".column-applicant").text();
                                        columns[6].innerHTML   = "<a href=\"tel:" + row.find(".column-phone").text() + "\">" + row.find(".column-phone").text() + "</a>";
                                        $("#modal-reject").modal("show");
                                    };
                                    $("#modal-reject button.btn-danger").click(function() {
                                        $.ajax({
                                            url: "?page=apply",
                                            method: "POST",
                                            data: { reject: postingData.id },
                                            dataType: "json",
                                            async: true,
                                            success: function() {
                                                $("#row-" + postingData.id).find(".column-status").removeClass("label-primary").addClass("label-danger").html("<i class=\\"fa fa-fw fa-times\\"></i><span class=\\"hidden-xs\\"> 已駁回</span>");
                                                $("#row-" + postingData.id).find(".column-accepting a").hide();
                                                $("#modal-reject").modal("hide");
                                            }
                                        });
                                    });
                                    window.checkConflicts = function() {
                                        var count = 0;
                                        $("tr.danger").removeClass("danger");
                                        $("tr .fa-exclamation-triangle").parents("tr").addClass("danger");
                                        $("tr.active").each(function() {
                                            ++count;
                                            $.ajax({
                                                url: "?page=conflict",
                                                method: "POST",
                                                data: { id: $(this).find(".column-id").text(), pending: true },
                                                dataType: "json",
                                                async: true,
                                                success: function(conflicts) {
                                                    for (var i = 0; i < conflicts.length; ++i) {
                                                        TableTools.fnGetInstance("dataset").fnDeselect($("#row-" + conflicts[i].id).addClass("danger"));
                                                    }
                                                }
                                            });
                                        });
                                        $(".btn-selection-accepting").removeClass("hide");
                                        if (count) $(".btn-selection-accepting span").text(count);
                                        else $(".btn-selection-accepting").addClass("hide");
                                    };
                                    $((new $.fn.dataTable.TableTools("#dataset", {
                                        aButtons: [],
                                        sRowSelect: "multi",
                                        fnRowSelected: window.checkConflicts,
                                        fnRowDeselected: window.checkConflicts,
                                        fnPreRowSelect: function(event, nodes) { return !$(nodes).hasClass("danger"); }
                                    })).fnContainer()).insertBefore("#dataset");
                                    window.checkConflicts();
                                    $("[data-toggle=confirmation]").confirmation();
                                    $(".btn-selection-accepting").click(function() {
                                        var selection = TableTools.fnGetInstance("dataset").fnGetSelectedData(), total = selection.length;
                                        $(selection).each(function() {
                                            var id = this[0];
                                            $.ajax({
                                                url: "?page=apply",
                                                method: "POST",
                                                data: { data: JSON.stringify({ id: id }) },
                                                dataType: "json",
                                                async: true,
                                                success: function() {
                                                    if (--total === 0) location.reload();
                                                }
                                            });
                                        });
                                    });' : '') . ($page === 'seat' ? '
                                    window.dashboardEnable = function(id) {
                                        dashboardToggle(id, true);
                                    };
                                    window.dashboardDisable = function(id) {
                                        dashboardToggle(id, false);
                                    };
                                    window.dashboardToggle = function(id, state) {
                                        postingData.path = "?page=seat";
                                        postingData.id = id;
                                        postingData.state = state;
                                        var row = $("#row-" + id), columns = $("#modal .modal-body span");
                                        columns[0].textContent = row.find(".place-name-display").text();
                                        columns[1].textContent = row.find(".column-disabled span").text().trim();
                                        columns[2].textContent = row.find(".column-toggle").text();
                                        $("#modal").modal("show");
                                    };
                                    window.dashboardEdit = function(id) {
                                        var a = $("#place-name-display-" + id),
                                            b = $(".hide#place-name-edit-" + id),
                                            c = $("#place-name-edit-button-" + id);
                                        if (b.length > 0) {
                                            b.removeClass("hide");
                                            a.addClass("hide");
                                            c.addClass("hide");
                                            b.find("input").val(a.text());
                                        }
                                    };
                                    window.dashboardSave = function(id) {
                                        var a = $(".hide#place-name-display-" + id),
                                            b = $("#place-name-edit-" + id),
                                            c = $(".hide#place-name-edit-button-" + id),
                                            d = b.find("input").val();
                                        if (a.length > 0) {
                                            $.ajax({
                                                url: "?page=seat",
                                                method: "POST",
                                                data: { id: id, save: d },
                                                dataType: "json",
                                                async: true,
                                                success: function() {
                                                    c.removeClass("hide");
                                                    a.removeClass("hide");
                                                    b.addClass("hide");
                                                    a.text(d);
                                                }
                                            });
                                        }
                                    };
                                    window.dashboardCancel = function(id) {
                                        var a = $(".hide#place-name-display-" + id),
                                            b = $("#place-name-edit-" + id),
                                            c = $(".hide#place-name-edit-button-" + id);
                                        c.removeClass("hide");
                                        a.removeClass("hide");
                                        b.addClass("hide");
                                    };' : '') . '
                                    $("#modal button.btn-primary").click(function() {
                                        $.ajax({
                                            url: postingData.path,
                                            method: "POST",
                                            data: {data:JSON.stringify(postingData)},
                                            dataType: "json",
                                            async: true,
                                            success: postingCallback
                                        });
                                    });
                                });
                            </script>
                        </div>
                    </div>
                ';
            $content['main'] = $html;
            break;
        case 'notice':
            $success = false;
            if ($is_post && isset($_POST['zh-tw']) && isset($_POST['en-us'])) {
                $notice_update = $db->prepare('UPDATE `notice` SET `zh-tw` = :tw, `en-us` = :en');
                $notice_update->bindValue(':tw', $_POST['zh-tw']);
                $notice_update->bindValue(':en', $_POST['en-us']);
                $notice_update->execute();
                $success = true;
            }
            $notice_query = $db->prepare('SELECT `zh-tw`, `en-us` FROM `notice`');
            $notice_query->execute();
            $notice = $notice_query->fetch();
            $content['main'] = ($success ? '
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-fw fa-check"></i> 儲存成功！</h4>
                        申請須知已儲存完畢。
                    </div>' : '') . '
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">編輯申請須知</h3>
                                </div><!-- /.box-header -->
                                <form role="form" action="?page=notice" method="post">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="textarea-zh-tw">正體中文</label>
                                            <textarea class="form-control" id="textarea-zh-tw" name="zh-tw" placeholder="請輸入中文版申請須知。" rows="16">' . htmlentities($notice['zh-tw']) . '</textarea>
                                        </div><!-- /.form-group -->
                                        <div class="form-group">
                                    </div><!-- /.box-body -->
                                    <div class="box-footer">
                                        <button class="btn btn-primary" type="submit">儲存</button>
                                    </div><!-- /.box-footer -->
                                </form><!-- /.form -->
                            </div><!-- /.box -->
                        </div>
                    </div>
                    <script src="https://cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script>
                    <script>$(function() { CKEDITOR.replace("textarea-zh-tw"); CKEDITOR.replace("textarea-en-us"); });</script>
                ';
            break;
    }
    $html = '<!DOCTYPE html>
<html lang="zh-tw">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>' . ($page === 'main' ? '後台首頁' : $content['title']) . ' | 佛光大學座位預約系統</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
        <meta name="theme-color" content="#3b8ab8" />
        <meta name="msapplication-navbutton-color" content="#3b8ab8" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
        <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css" />
        <link rel="stylesheet" href="plugins/datatables/extensions/TableTools/css/dataTables.tableTools.min.css" />
        <link rel="stylesheet" href="plugins/jvectormap/jquery-jvectormap-1.2.2.css" />
        <link rel="stylesheet" href="dist/css/AdminLTE.min.css" />
        <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css" />
        <link rel="stylesheet" href="/css/pending.css" />
        <link rel="icon" type="image/png" href="/images/logo.png" />
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="plugins/fastclick/fastclick.min.js"></script>
        <script src="dist/js/app.min.js"></script>
        <script src="plugins/sparkline/jquery.sparkline.min.js"></script>
        <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
        <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
        <script src="plugins/chartjs/Chart.min.js"></script>
        <script src="plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
        <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
        <script src="/js/bootstrap-confirmation.min.js"></script>
        <style>
            @media (min-width: 768px) {
                .main-header .sidebar-toggle {
                    display: none;
                }
            }
        </style>
    </head>
    <body class="hold-transition skin-blue-light sidebar-mini page-' . $page . '">
        <div class="wrapper">
            <header class="main-header">
                <a href="./" class="logo">
                    <span class="logo-lg"><b>FGU</b>SG</span>
                </a>
                <nav class="navbar navbar-static-top" role="navigation">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">顯示/隱藏選單</span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li>
                                <a id="time-remaining" href="javascript:;" title="點選此處重設鎖定時間">帳號將於 <code></code> 內鎖定<span class="hidden-xs">，點選此處重設鎖定時間</span></a>
                            </li>
                            <li>
                                <a href="logout.php"><i class="fa fa-sign-out"> 登出</i></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="/images/logo.png" class="img-circle" alt="FGU Logo" />
                        </div>
                        <div class="pull-left info">
                            <p>' . $content['username'] . '</p>
                            <a href="logout.php"><i class="fa fa-fw fa-sign-out text-success"></i> 登出後台</a>
                        </div>
                    </div>
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="header">後台選單</li>
                        <li' . ($page === 'main'                            ? ' class="active"' : '') . '><a href="?page=main"><i class="fa fa-fw fa-dashboard"></i> 後台首頁</a></li>
                        <li' . ($page === 'apply'                           ? ' class="active"' : '') . '><a href="?page=apply"><i class="fa fa-fw fa-sticky-note-o"></i> 審核申請</a></li>
                        <li' . ($page === 'application'                     ? ' class="active"' : '') . '><a href="?page=application"><i class="fa fa-fw fa-history"></i> 申請記錄</a></li>
                        <li' . ($page === 'seat' || $page === 'config' ? ' class="active"' : '') . '><a href="?page=seat"><i class="fa fa-fw fa-cog"></i> 座位設定</a></li>
                        <li' . ($page === 'notice'                          ? ' class="active"' : '') . '><a href="?page=notice"><i class="fa fa-fw fa-info-circle"></i> 申請須知</a></li>
                        <li class="header">返回借用系統</li>
                        <li><a href="/"><i class="fa fa-fw fa-angle-double-left"></i> 返回借用系統</a></li>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        ' . $content['title'] . '
                        <small>管理後台</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./"><i class="fa fa-fw fa-dashboard"></i> 管理後台</a></li>' . ($page === 'config' ? '
                        <li><a href="?page=seat">' . $pages['seat'] . '</a></li>
                        <li class="active"><a href="?page=config&amp;id=' . $id . '">' . $pages['config'] . '</a></li>' : '
                        <li class="active"><a href="?page=' . $page . '">' . $content['title'] . '</a></li>') . '
                    </ol>
                </section>
                <section class="content">
                    <div class="callout callout-info">
                        <h4>提醒：將不會自動載入最新資料</h4>
                        <p>因技術問題，若資料有更新將無法即時顯示，請您定期重新整理以確保您所檢視的資料為最新的狀態！</p>
                    </div>' . $content['main'] . '
                </section>
            </div><!-- /.content-wrapper -->
            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    本後台採用 <a href="https://almsaeedstudio.com" title="Almsaeed Studio"><b>Admin</b>LTE</a> 主題
                </div>
                <strong>Copyright &copy; 2016 <a href="/" title="佛光大學學生會">FGUSG</a>.</strong> All rights reserved.
            </footer>
            <div class="control-sidebar-bg"></div>
        </div><!-- ./wrapper -->
        <script src="/js/pending.js"></script>
        <script src="/js/admin-time.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
        <script>moment.locale("zh-tw", { weekdaysMin: "日一二三四五六".split("") });</script>
    </body>
</html>
';
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Length: ' . strlen($html));
    echo($html);
    function times_to_str($times, $separator = ', ', $glue = '~', $t = null, $array = true) {
        if ($t === null) $t = array(
            'time1'  =>  '1',
        );
        $first = false;
        $last = false;
        $time = '';
        foreach ($t as $c => $d) {
            $b = $array ? $times[$c] : ($c & $times);
            if ($b && $last === false) {
                $first = true;
                if (!empty($time)) $time .= $separator;
                $time .= $d;
            } else {
                if (!$b && $last && !$first) {
                    if (!empty($time)) $time .= $glue;
                    $time .= $last;
                }
                $first = false;
            }
            $last = $b ? $d : false;
        }
        if ($last && !$first) {
            if (!empty($time)) $time .= $glue;
            $time .= $last;
        }
        return $time;
    }
    function check_acceptable($id) {
        return count(get_conflicts($id)) === 0;
    }
    function get_conflicts($id) {
        $db = database_get();
        $apply_query = $db->prepare('SELECT `seat`, `date`, `time1` FROM `apply` WHERE `id` = :id');
        $apply_query->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $apply_query->execute();
        $apply = $apply_query->fetch();
        $conflicts = array();
        if ($apply) {
            $seat_query = $db->prepare('SELECT `id`, `name` FROM `seat`');
            $seat_query->execute();
            $seat = array();
            while (false !== ($row = $seat_query->fetch()))
                $seat[$row['id']] = $row['name'];
            $accepted_apply_query = $db->prepare('SELECT `id`, `sid`, `seat`, `date`, `time1`, `organization`, `applicant`, `phone` FROM `apply` WHERE `seat` = :seatm AND `date` = :date AND `status` = 1');
            $accepted_apply_query->bindValue(':seat', (int) $apply['seat'], PDO::PARAM_INT);
            $accepted_apply_query->bindValue(':date', $apply['date']);
            $accepted_apply_query->execute();
            while (false !== ($accepted_apply = $accepted_apply_query->fetch())) {
                $tc = array('time1'');
                foreach ($tc as $c) if ($apply[$c] > 0 && $accepted_apply[$c] > 0) {
                    $time = times_to_str($apply);
                    if (empty($time)) $time = '（無時段）';
                    if ($time === '1~D') $time = '整天';
                    $conflicts[] = array(
                        'id'           => (int) $accepted_apply['id'],
                        'sid'          =>       $accepted_apply['sid'],
                        'place'        =>       $seat[$accepted_apply['seat']],
                        'date'         =>       $accepted_apply['date'],
                        'time'         =>       $time,
                        'organization' =>       $accepted_apply['organization'],
                        'applicant'    =>       $accepted_apply['applicant'],
                        'phone'        =>       $accepted_apply['phone'],
                    );
                    break;
                }
            }
        }
        return $conflicts;
    }
    function get_pending_conflicts($id) {
        $db = database_get();
        $apply_query = $db->prepare('SELECT `seat`, `date`, `time1`FROM `apply` WHERE `id` = :id');
        $apply_query->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $apply_query->execute();
        $apply = $apply_query->fetch();
        $conflicts = array();
        if ($apply) {
            $seat_query = $db->prepare('SELECT `id`, `name` FROM `seat`');
            $seat_query->execute();
            $seat = array();
            while (false !== ($row = $seat_query->fetch()))
                $seat[$row['id']] = $row['name'];
            $pending_apply_query = $db->prepare('SELECT `id`, `sid`, `seat`, `date`, `time1`, `organization`, `applicant`, `phone` FROM `apply` WHERE `id` != :id AND `seat` = :seat AND `date` = :date AND `status` = 0');
            $pending_apply_query->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $pending_apply_query->bindValue(':seat', (int) $apply['seat'], PDO::PARAM_INT);
            $pending_apply_query->bindValue(':date', $apply['date']);
            $pending_apply_query->execute();
            while (false !== ($pending_apply = $pending_apply_query->fetch())) {
                $tc = array('time1', 'time2', 'time3', 'time4', 'time5', 'time6', 'time7', 'time8', 'time9', 'time10', 'timeA', 'timeB', 'timeC', 'timeD');
                foreach ($tc as $c) if ($apply[$c] > 0 && $pending_apply[$c] > 0) {
                    $time = times_to_str($pending_apply);
                    if (empty($time)) $time = '（無時段）';
                    if ($time === '1~D') $time = '整天';
                    $conflicts[] = array(
                        'id'           => (int) $pending_apply['id'],
                        'sid'          =>       $pending_apply['sid'],
                        'place'        =>       $seat[$pending_apply['seat']],
                        'date'         =>       $pending_apply['date'],
                        'time'         =>       $time,
                        'organization' =>       $pending_apply['organization'],
                        'applicant'    =>       $pending_apply['applicant'],
                        'phone'        =>       $pending_apply['phone'],
                    );
                    break;
                }
            }
        }
        return $conflicts;
    }