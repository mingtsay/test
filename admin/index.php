<?php
	include_once('include.inc.php') ;

	if(!$_SESSION['admin_login']) redirect('login.php') ;
	if(time()-$_SESSION['admin_ts'] > 600) redirect('lockscreen.php');
	$_SESSION['admin_ts'] = time() ;

	$pages = array(
		'main' 			=> '首頁',
		'block' 		=> '黑名單' ,
		'config' 		=> '座位設定 ',
		'application' 	=> '申請紀錄' ,
		'notice'		=> '申請須知',
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
			$apply_query = $db -> prepare('SELECT COUNT(*) AS `c`FROM ``')
	}