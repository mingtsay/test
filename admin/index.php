<?php
	include_once('include.inc.php') ;

	if(!$_SESSION['admin_login']) redirect('login.php') ;
	if(time()-$_SESSION['admin_ts'] > 600) redirect('lockscreen.php');
	$_SESSION['admin_ts'] = time() ;

	$pages = array(
		'main' 			=> '首頁',
		'block' 		=> '黑名單',
		'config' 		=> '座位詳細設定 ', //設定場地的細部設定 例如部分時段不開放
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

            $content['main'] ='

            	<div class= "row">
            		<div class ="col-lg-3 col-xs-6">
            			<div class = "inner">
            				<h3>' . apply_count['c'] .'</h3>
            				<p>待審核申請</p>
            			</div>
            			<div>
            				<i class="ion ion-ios-paper"></i>
            			</div>
            			<a href="?/page=apply" class="small-box-footer">前往申請<i class="fa fa-fw fa-arrow-circle-right"></i></a>
            		</<div>
            	</div>
            	
            '
	}