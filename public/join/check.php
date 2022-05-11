<?php

require(__DIR__ . '/../../app/config.php');

use App\Database;
use App\Token;
use App\Register;
use App\Utils;

Token::create();

if (isset($_SESSION['form'])) {
	$form = $_SESSION['form'];
} else {
	header('Location: ' . JOIN_URL);
	exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	Token::validate();

	$pdo = Database::getInstance();

	try {
		$pdo->beginTransaction();
		$register = new Register($pdo, $form);
		$register->userCreate();
		$register->memberCreate();
		$pdo->commit();
	} catch (PDOException $e) {
		$pdo->rollback();
		$_SESSION['error_register'] = 'register';
		header('Location: ' . JOIN_URL);
		exit();
	}

	unset($_SESSION['form']);
	header('Location: ' . THANKS_URL);
}

// header
require_once(__DIR__ . '/../../app/_parts/_header.php');
?>

<h2 class="section-title">
	記入した内容を確認して、<br>
	「登録する」ボタンをクリックしてください
</h2>
<section class="check">
	<form action="" method="post">
		<input type="hidden" name="token" value="<?php echo Utils::h($_SESSION['token']); ?>">
		<dl class="register-list">
			<dt>ユーザーID</dt>
			<dd><?php echo Utils::h($form['name']); ?></dd>
			<dt>メールアドレス</dt>
			<dd><?php echo Utils::h($form['email']); ?></dd>
			<dt>パスワード</dt>
			<dd>【非表示】</dd>
			<dt>二人の名前</dt>
			<dd class="select-gender">
				<label>
					<i class="bi bi-gender-male text-primary"></i>
					<span><?php echo Utils::h($form['male_name']); ?></span>
				</label>
				<label>
					<i class="bi bi-gender-female text-danger"></i>
					<span><?php echo Utils::h($form['female_name']); ?></span>
				</label>
			</dd>
		</dl>
		<div class="check-buttons">
			<a href="index.php?action=rewrite" class="btn btn-outline-success">やり直す</a>
			<span>|</span>
			<button class="btn btn-outline-success">登録する</button>
	</form>
</section>

<!-- footer -->
<?php require_once(__DIR__ . '/../../app/_parts/_footer.php');
