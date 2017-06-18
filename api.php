<?php
	//date
	date_default_timezone_set('Asia/Taipei') ;

	//session
	session_name('FGUSG_SESSION') ;
	session_set_cookie_params(31536000,'/',$_SERVER['HTTP_HOST'],false,true) ;
	session_start();
	setcookie(session_name(),session_id(),session_time()+31536000,'/',$_SERVER['HTTP_HOST'],false,true) ;
	if(!isset($_SESSION['login'])) $_SESSION['login'] = false ;
	if(!isset($_SESSION['login_ts'])) $_SESSION['login_ts'] = 0 ;

	//headers
	header('X-Powered-By : FGUSG') ;

	//variables
	$okay = true ;
	$json = array() ;

	//fuctions 
	
	function recaptcha_verify($recaptcha_response) {
        return json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create(array('http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query(array(
                'secret'   => '6Lf5jCUUAAAAALEvCuLxboIHNRWVhP2A89Scgpxi',
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
        )))))), true)['success'];
    }

    function database_get(){
    	if(!isset($GLOBALS['__PDO_DB__'])){
    		$GLOBALS['__PDO_DB__'] = new PDO('mysql:host = 127.4.153.2; dbname = seat-fgusg','adminfiseAcE','TKaN4PCDwDP4');
    		$GLOBALS['__PDO_DB__'] ->exec('SET NAMES UTF8');
    	}
    	return $$GLOBALS['__PDO_DB__'] ;
    }

    function times_to_str($times, $separator = ', ', $glue = '~', $t = null, $array = true) {
    	if($t===null)
    	{
    		$t= array('time1' =>'1', );
    	}
    }

    $first = false ;
    $last = false  ;
    $time = '' ; 

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

    // auto logout

    if($_SESSION['login']){
    	if(time() - $_SESSION['login_ts'] >600){
    		$_SESSION['login'] = false ; 
    	}
    }

    //api code

    if($_SERVER['REQUEST_METHOD']==='POST')
    {
    	$db = database_get();
        switch ($_POST['action']) {
            case 'is_login':
                $json = array('is_login' => $_SESSION['login']);
                break;
            case 'get_timeout':
                $json = array('timeout' => $_SESSION['login'] ? max(600 - time() + $_SESSION['login_ts'], 0) : 0);
                break;
            case 'login':
                if ($_SESSION['login']) {
                    $okay = false;
                    break;
                }
                $sid = isset($_POST['sid']) ? $_POST['sid'] : '';
                $pw  = isset($_POST['pw'])  ? $_POST['pw']  : '';
                $recaptcha = isset($_POST['recaptcha']) ? $_POST['recaptcha'] : '';
                if (recaptcha_verify($recaptcha)) {
                    if (
                        ($sid === 'root' && hash('sha256', $pw . 'qDizIBEx') === 'a42d45917a0877d961c9d5361177d16608a6c1f0f83c36f9c286b43c4d116504') ||
                        ($sid === 'admin' && $pw === 'acc1661') ||
                        student_verift($sid, $pw)
                    ) {
                        $_SESSION['login']    = true;
                        $_SESSION['login_ts'] = time();
                        $_SESSION['sid']      = ($sid === 'root' || $sid === 'admin') ? $sid : strtoupper($sid);
                    }
                }
                $json = array('login' => $_SESSION['login']);
                break;
    		case 'logout' :
    			$_SESSION['login'] = false ;
    			$json = array('logout' => true);
    			break ;
    		case 'status_get' :
    			$seat_query = $db->prepare('SELECT `id`,`name`FROM `seat` WHERE `disabled` = 0 ');
    			$seat_query->execute();
    			$seat = array();
    			while (false!==($row=$seat_query->fetch())) 
    				$seat[$row['id']] =$row['name'] ;
    			
    			$t = array('time1') ;

    			$status = array();
    			$status_blank = array() ;
    			foreach ($seat as $seat_id => $seat) {
    				$status_blank[$seat_id] = array();
    				for($k = 0;$k < count($t);++$k){
    					$status_blank[$seat_id][$k] =-1 ; 
    				}
    			}

                $rules_query = $db->prepare('SELECT `seat`, `type`, `time1`,`start`, `end` FROM `seat_rules` WHERE (`start` >= DATE(NOW()) AND `end` IS NULL) OR `end` >= DATE(NOW()) ORDER BY `start` ASC, `seat` ASC');
                $rules_query->execute();
                while (false !== ($rules = $rules_query->fetch())) {
                    if (!isset($seat[$rules['seat']]) && $rules['seat'] != 0) continue;
                    switch ((int) $rules['type']) {
                        case 0:
                            if (!isset($status[$rules['start']])) $status[$rules['start']] = $status_blank;
                            for ($k = 0; $k < count($t); ++$k) {
                                if ($rules[$t[$k]] != 0) {
                                    if ($rules['seat'] != 0) {
                                        $status[$rules['start']][$rules['seat']][$k] = -2;
                                    } else {
                                        foreach ($seat as $seat_id => $seat_name) {
                                            $status[$rules['start']][$seat_id][$k] = -2;
                                        }
                                    }
                                }
                            }
                            break;
                        case 1:
                        case 2:
                            for ($d = strtotime($rules['start']); $d <= strtotime($rules['end']); $d += 86400) {
                                if (2 === (int) $rules['type'] && (pow(2, (int) date('w', $d)) & (int) $rules['weekday']) === 0) continue;
                                $date = date('Y-m-d', $d);
                                if (!isset($status[$date])) $status[$date] = $status_blank;
                                for ($k = 0; $k < count($t); ++$k) {
                                    if ($rules[$t[$k]] != 0) {
                                        if ($rules['seat'] != 0) {
                                            $status[$date][$rules['seat']][$k] = -2;
                                        } else {
                                            foreach ($seat as $seat_id => $seat_name) {
                                                $status[$date][$seat_id][$k] = -2;
                                            }
                                        }
                                    }
                                }
                            }
                            break;
                    }
                } 
                $apply_query = $db->prepare('SELECT `id`, `seat`, `date`, `time1`,`status` FROM `apply` WHERE `date` >= DATE(NOW()) AND `status` < 2 ORDER BY `status` DESC, `date` ASC, `seat ASC');
                $apply_query->execute();
                while (false !== ($apply = $apply_query->fetch())) {
                    if (!isset($seat[$apply['seat']])) continue;
                    if (!isset($status[$apply['date']])) $status[$apply['date']] = $status_blank;
                    for ($k = 0; $k < count($t); ++$k) {
                        if ($apply[$t[$k]] != 0 && $status[$apply['date']][$apply['seat']][$k] < 0) {
                            if ($status[$apply['date']][$apply['seat']][$k] !== -2 || (int) $apply['status'] > 0)
                                $status[$apply['date']][$apply['seat']][$k] = (int) $apply['status'] > 0 ? (int) $apply['id'] : 0;
                        }
                    }
                }
                $json = array('seat' => $seat, 'status' => $status);
                break;
            case 'status_detail': //編輯段落點 日後回補用
                $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
                $apply_query = $db->prepare('SELECT `seat`, `date`, `time1`,`organization`, `applicant` FROM `apply` WHERE `id` = :id AND `date` >= DATE(NOW()) AND `status` = 1');
                $apply_query->bindValue(':id', (int) $id, PDO::PARAM_INT);
                $apply_query->execute();
                $apply = $apply_query->fetch();
                if ($apply) {
                    $seat_query = $db->prepare('SELECT `name` FROM `seat` WHERE `id` = :id AND `disabled` = 0');
                    $seat_query->bindValue(':id', (int) $apply['seat'], PDO::PARAM_INT);
                    $seat_query->execute();
                    $seat = $seat_query->fetch();
                    $json = array(
                        'seat'    => $seat['name'],
                        'date'         => $apply['date'],
                        'time'         => times_to_str($apply),
                        'organization' => $apply['organization'],
                        'applicant'    => $apply['applicant'],
                    );
                } else $json = false;
                break;
            default:
                if ($_SESSION['login']) {
                    $_SESSION['login_ts'] = time();
                    switch ($_POST['action']) {
                        case 'notice_get':
                            $query = $db->prepare('SELECT `' . ($_POST['lang'] === 'zh-tw' ? 'zh-tw' : 'en-us') . '` AS `notice` FROM `notice`');
                            $query->execute();
                            $notice = $query->fetch();
                            $json = array('notice' => $notice['notice']);
                            break;
                        case 'user_get_information':
                            $json = array('user_information' => $_SESSION['sid']);
                            break;
                        case 'seat_get_information':
                            $json = array('seat_information' => false);
                            $query = $db->prepare('SELECT `id`, `name`, `disabled` FROM `seat` WHERE `disabled` = 0');
                            $query->execute();
                            if ($seat_list = $query->fetchAll()) {
                                $json['seat_information'] = array();
                                foreach ($seat_list as $seat) {
                                    $seat = array(
                                        'id'             =>      $seat['id'],
                                        'name'           =>      $seat['name'],
                                    );
                                    $json['seat_information'][] = $seat;
                                }
                            }
                            break;
                        case 'apply_save':
                            $json = array('apply_save' => true);
                            $seat    = isset($_POST['seat'])    ? (int) $_POST['seat']     :  0;
                            $date         = isset($_POST['date'])         ?  trim($_POST['date'])         : '';
                            $time         = isset($_POST['time'])         ? (int) $_POST['time']          :  0;
                            $organization = isset($_POST['organization']) ?  trim($_POST['organization']) : '';
                            $applicant    = isset($_POST['applicant'])    ?  trim($_POST['applicant'])    : '';
                            $phone        = isset($_POST['phone'])        ?  trim($_POST['phone'])        : '';
                            $seat_query = $db->prepare('SELECT `id` FROM `seat` WHERE `id` = :seat');
                            $seat_query->bindValue(':seat', $seat, PDO::PARAM_INT);
                            $seat_query->execute();
                            $seat_result = $seat_query->fetch();
                            $date_ts = strtotime($date);
                            $today = mktime(0, 0, 0);
                            if (
                                !$seat_result ||
                                $time === 0 || empty($date) || empty($organization) || empty($applicant) || empty($phone) ||
                                $date_ts === false || $date_ts <= $today //|| $date_ts >= $today + 2592000 // 30 days
                            ) {
                                $json['apply_save'] = false;
                                break;
                            }
                            $apply_save = $db->prepare('INSERT INTO `apply` (`sid`, `seat`, `date`, `time1`,`organization`, `applicant`, `phone`, `ip`, `ts`) VALUES (:sid, :seat, :date, :time1, :time2, :time3, :time4, :time5, :time6, :time7, :time8, :time9, :time10, :timeA, :timeB, :timeC, :timeD, :organization, :applicant, :phone, :ip, :ts)');
                            $apply_save->bindValue(':sid', $_SESSION['sid']);
                            $apply_save->bindValue(':seat', $seat, PDO::PARAM_INT);
                            $apply_save->bindValue(':date', $date);
                            $apply_save->bindValue(':time1',  !!($time &    1), PDO::PARAM_BOOL);
                            $apply_save->bindValue(':organization', $organization);
                            $apply_save->bindValue(':applicant', $applicant);
                            $apply_save->bindValue(':phone', $phone);
                            $apply_save->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
                            $apply_save->bindValue(':ts', time(), PDO::PARAM_INT);
                            $apply_save->execute();
                            break;
                        case 'cancel_get_list':
                            $json = array('cancel_list' => false);
                            $seat_query = $db->prepare('SELECT `id`, `name` FROM `seat`');
                            $seat_query->execute();
                            $seat = array();
                            while (false !== ($row = $seat_query->fetch()))
                                $seat[$row['id']] = $row['name'];
                            $list_query = $db->prepare('SELECT `id`, `seat`, `date`, `time1`, `time2`, `time3`, `time4`, `time5`, `time6`, `time7`, `time8`, `time9`, `time10`, `timeA`, `timeB`, `timeC`, `timeD`, `status` FROM `apply` WHERE `sid` = :sid AND `date` >= DATE(NOW()) AND (`status` = 0 OR `status` = 1)');
                            $list_query->bindValue(':sid', $_SESSION['sid']);
                            $list_query->execute();
                            if ($list = $list_query->fetchAll()) {
                                $json['cancel_list'] = array();
                                foreach ($list as $row) {
                                    $json['cancel_list'][] = array(
                                        'id'        => (int) $row['id'],
                                        'seat' =>       $seat[$row['seat']],
                                        'date'      =>       $row['date'],
                                        'time'      =>       times_to_str($row),
                                        'status'    => (int) $row['status'],
                                    );
                                }
                            }
                            break;
                        case 'cancel':
                            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
                            $json = array('cancel' => false);
                            $cancel_query = $db->prepare('SELECT `status` FROM `apply` WHERE `id` = :id AND `sid` = :sid AND `date` >= DATE(NOW()) AND (`status` = 0 OR `status` = 1)');
                            $cancel_query->bindValue(':id', $id, PDO::PARAM_INT);
                            $cancel_query->bindValue(':sid', $_SESSION['sid']);
                            $cancel_query->execute();
                            if ($cancel = $cancel_query->fetch()) {
                                $cancel_update = $db->prepare('UPDATE `apply` SET `status` = :status WHERE `id` = :id LIMIT 1');
                                $cancel_update->bindValue(':id', $id, PDO::PARAM_INT);
                                $cancel_update->bindValue(':status', 2 | $cancel['status'], PDO::PARAM_INT);
                                $cancel_update->execute();
                                $json['cancel'] = true;
                            }
                            break;
                        case 'record_get_list':
                            $json = array('record_list' => false);
                            $seat_query = $db->prepare('SELECT `id`, `name` FROM `seat`');
                            $seat_query->execute();
                            $seat = array();
                            while (false !== ($row = $seat_query->fetch()))
                                $seat[$row['id']] = $row['name'];
                            $list_query = $db->prepare('SELECT `id`, `seat`, `date`, `time1`,`status` FROM `apply` WHERE `sid` = :sid ORDER BY `date` DESC');
                            $list_query->bindValue(':sid', $_SESSION['sid']);
                            $list_query->execute();
                            if ($list = $list_query->fetchAll()) {
                                $json['record_list'] = array();
                                foreach ($list as $row) {
                                    $json['record_list'][] = array(
                                        'id'        => (int) $row['id'],
                                        'seat' =>       $seat[$row['seat']],
                                        'date'      =>       $row['date'],
                                        'time'      =>       times_to_str($row),
                                        'status'    => (int) $row['status'],
                                    );
                                }
                            }
                            break;
                        default: $okay = false;
                    }
                } else $okay = false;
        }
    } else $okay = false;
    if (!$okay) header('HTTP/ 403');
    else {
        $json_str = json_encode($json);
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Length: ' . $json_str);
        echo($json_str);
    }                   			
    }  