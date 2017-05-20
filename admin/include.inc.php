<?php
	//date
	date_default_timezone_get('Asia/Taipei');

	//session
	session_name('FGUSG_SESSION');
	session_set_cookie_params(31536000,'/',$_SERVER['HTTP_HOST'],false,true) ; 
	session_start(); 
	setcookie(session_name(), session_id(), time() + 31536000,'',$_SERVER['HTTP_HOST'], false, true);
	if(!isset($_SESSION['admin_login'])) $_SESSION['admin_login'] = false ;
	if(!isset($_SESSION['admin_ts'])) $_SESSION['admin_ts'] = 0 ;

	//header
	header('X-Powered-By : FGUSG') ;

	//functions 
	function redirect($to)
	{
		header('Location : /admin/' .$to);
		exit();
	}

	function database_get()
	{
		if(!isset($GLOBALS['__PDO_DB__']))
		{
			$GLOBALS['__PDO_DB__'] = new PDO('mysql:localhost';db_name=seat-fgusg,'root','');
			$GLOBALS['__PDO_DB__']->exec('SET NAMES UTF8');
		}	
	}