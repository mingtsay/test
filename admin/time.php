<?php
    include_once('include.inc.php');
    $time = time();
    $timeout = 600;
    if (!$_SESSION['admin_login'] || (isset($_GET['reset']) && $time - $_SESSION['admin_ts'] > $timeout)) {
        header('Content-Length: 0');
        header('HTTP/ 403');
        exit();
    }
    if (isset($_GET['reset'])) $_SESSION['admin_ts'] = $time;
    $json = array(
        'time'    => $time,
        'access'  => $_SESSION['admin_ts'],
        'timeout' => $timeout,
        'remain'  => $timeout + $_SESSION['admin_ts'] - $time,
    );
    $json_raw = json_encode($json);
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Length: ' . $json_raw);
    echo($json_raw);