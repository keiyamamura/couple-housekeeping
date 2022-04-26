<?php

session_start();

date_default_timezone_set('Asia/Tokyo');

define('DSN', 'mysql:host=localhost:8889;dbname=portfolio;charset=utf8mb4');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('JOIN_URL', 'http://localhost:8888/portfolio/public/join/');
define('CHECK_URL', 'http://localhost:8888/portfolio/public/join/check.php');
define('REWRITE_URL', 'http://localhost:8888/portfolio/public/join/index.php?action=rewrite');
define('THANKS_URL', 'http://localhost:8888/portfolio/public/join/thanks.php');
define('LOGIN_URL', 'http://localhost:8888/portfolio/public/join/login.php');
define('MAIN_URL', 'http://localhost:8888/portfolio/public/index.php');

require_once(__DIR__ . '/functions.php');
