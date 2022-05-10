<?php
require(__DIR__ . '/../app/config.php');

unset($_SESSION['id']);
unset($_SESSION['name']);
unset($_SESSION['date']);
unset($_SESSION['error_register']);

header('Location: ' . LOGIN_URL);
exit();
