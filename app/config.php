<?php

session_start();

date_default_timezone_set('Asia/Tokyo');

// heroku 登録時のDB情報
// define('DSN', 'mysql:host=us-cdbr-east-05.cleardb.net;dbname=heroku_6a79aeb16384b48;charset=utf8mb4');
// define('DB_USER', 'be2b50b44c02c4');
// define('DB_PASS', 'f00ad98e');

// ローカル環境
define('DSN', 'mysql:host=localhost;dbname=portfolio;charset=utf8mb4');
define('DB_USER', 'root');
define('DB_PASS', 'root');

define('JOIN_URL', 'http://couple-housekeeping.herokuapp.com/join/');
define('CHECK_URL', 'http://couple-housekeeping.herokuapp.com/join/check.php');
define('REWRITE_URL', 'http://couple-housekeeping.herokuapp.com/join/index.php?action=rewrite');
define('THANKS_URL', 'http://couple-housekeeping.herokuapp.com/join/thanks.php');
define('LOGIN_URL', 'http://couple-housekeeping.herokuapp.com/join/login.php');
define('MAIN_URL', 'http://couple-housekeeping.herokuapp.com/index.php');

spl_autoload_register(function ($class) {
	$prefix = 'App\\';

	if (strpos($class, $prefix) === 0) {
		$filename = sprintf(__DIR__ . '/%s.php', substr($class, strlen($prefix)));

		if (file_exists($filename)) {
			require($filename);
		} else {
			echo 'File not found: ' . $filename;
			exit();
		}
	}
});
