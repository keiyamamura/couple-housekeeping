<?php

use App\Utils;

$page_url = Utils::getCurrentPageUrl();
$check_url = [
	'join' => '',
	'after_login' => ''
];
switch ($page_url) {
	case JOIN_URL:
		$page_title = '会員登録';
		$check_url['join'] = JOIN_URL;
	break;
	case CHECK_URL:
		$page_title = '確認';
		$check_url['join'] = CHECK_URL;
	break;
	case REWRITE_URL:
		$page_title = '修正';
		$check_url['join'] = REWRITE_URL;
	break;
	case THANKS_URL:
		$page_title = '登録完了';
		$check_url['join'] = THANKS_URL;
	break;
	case LOGIN_URL:
		$page_title = 'ログイン';
		$check_url['join'] = LOGIN_URL;
	break;
	default:
		$page_title = Utils::h($get_name) . '様';
	break;
}
var_dump($page_url);
var_dump($check_url);
?>
<!doctype html>
<html lang="ja">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Potta+One&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
	<?php if ($page_url === $check_url['join']) : ?>
		<link rel="stylesheet" href="css/style.css">
	<?php else : ?>
		<link rel="stylesheet" href="../css/style.css">
	<?php endif; ?>
	<title>かけいぼ - <?php echo Utils::h($page_title); ?></title>
</head>

<body>
	<header>
		<div class="container">
			<h1 class="main-title">
				<a href="">かけいぼ</a>
			</h1>

			<nav class="navbar navbar-expand-md navbar-light">
				<div class="container">
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon">
						</span>
					</button>
					<div class="collapse navbar-collapse" id="navbarNav">
						<ul class="navbar-nav">
							<?php if ($page_url === $check_url['join']) : ?>
								<li class="nav-item">
									<a class="btn <?php echo $page_url !== LOGIN_URL ? 'disabled' : 'btn-outline-success'; ?>" href="<?php echo Utils::h(JOIN_URL); ?>">会員登録</a>
								</li>
								<li class="nav-item">
									<a class="btn <?php echo $page_url === LOGIN_URL ? 'disabled' : 'btn-outline-success'; ?>" href="<?php echo Utils::h(LOGIN_URL) ?>">ログイン</a>
								</li>
							<?php endif; ?>

							<?php if ($page_url !== $check_url['join']) : ?>
								<li class="nav-item">
									<a class="btn disabled"><?php echo Utils::h($get_name); ?> 様</a>
								</li>
								<li class="nav-item">
									<a class="btn btn-outline-success" href="logout.php">ログアウト</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</nav>
		</div>
	</header>

	<main>
		<div class="container">
