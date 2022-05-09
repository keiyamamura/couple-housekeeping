<?php

session_start();

date_default_timezone_set('Asia/Tokyo');

define('DSN', 'mysql:host=us-cdbr-east-05.cleardb.net;dbname=heroku_6a79aeb16384b48;charset=utf8mb4');
define('DB_USER', 'be2b50b44c02c4');
define('DB_PASS', 'f00ad98e');
define('JOIN_URL', 'books-portfolio.herokuapp.com/join/');
define('CHECK_URL', 'books-portfolio.herokuapp.com/join/check.php');
define('REWRITE_URL', 'books-portfolio.herokuapp.com/join/index.php?action=rewrite');
define('THANKS_URL', 'books-portfolio.herokuapp.com/join/thanks.php');
define('LOGIN_URL', 'books-portfolio.herokuapp.com/join/login.php');
define('MAIN_URL', 'https://books-portfolio.herokuapp.com/index.php');

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
