<?php
require(__DIR__ . '/../app/config.php');

unset($_SESSION['id']);
unset($_SESSION['name']);
unset($_SESSION['date']);

header('Location: ' . LOGIN_URL);
exit();
