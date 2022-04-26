<?php

require(__DIR__ . '/../../app/config.php');

createToken();

if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
	$form = $_SESSION['form'];
} else {
	$form = [
		'name' => '',
		'email' => '',
		'password' => '',
		'male_name' => '',
		'female_name' => '',
	];
}
$error = [];

// フォームの内容をチェック
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	validateToken();

	$form['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	if ($form['name'] === "") {
		$error['name'] = 'blank';
	}

	$form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	if ($form['email'] === '') {
		$error['email'] = 'blank';
	} else {
		$pdo = getPdoInstance();

		$stmt = $pdo->prepare("SELECT COUNT(*) AS email FROM users WHERE email = :email");
		$stmt->bindValue('email', $form['email'], PDO::PARAM_STR);
		$stmt->execute();

		$count = $stmt->fetch();
		if ($count->email > 0) {
			$error['email'] = 'duplicate';
		}
	}

	$form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	if ($form['password'] === '') {
		$error['password'] = 'blank';
	} elseif (strlen($form['password']) < 8) {
		$error['password'] = 'length';
	}

	$form['male_name'] = filter_input(INPUT_POST, 'male_name', FILTER_SANITIZE_STRING);
	$form['female_name'] = filter_input(INPUT_POST, 'female_name', FILTER_SANITIZE_STRING);
	if ($form['male_name'] === '' || $form['female_name'] === '') {
		$error['couple'] = 'blank';
	}

	$form['male_num'] = filter_input(INPUT_POST, 'male_num', FILTER_SANITIZE_NUMBER_INT);
	$form['female_num'] = filter_input(INPUT_POST, 'female_num', FILTER_SANITIZE_NUMBER_INT);
	if ($form['male_num'] !== '1' || $form['female_num'] !== '2') {
		$form['male_num'] = '1';
		$form['female_num'] = '2';
	}

	if (empty($error)) {
		$_SESSION['form'] = $form;
		header('Location: ' . CHECK_URL);
		exit();
	}
}

// header
require_once(__DIR__ . '/../../app/_parts/_header.php');
?>

<h2 class="section-title">次のフォームに必要事項をご記入ください</h2>
<section class="member-register">
	<form action="" method="post">
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
		<dl class="register-list">
			<dt>ユーザー名<span class="required">必須</span></dt>
			<dd>
				<input type="text" name="name" maxlength="255" value="<?php echo h($form['name']); ?>" />
				<?php if (isset($error['name']) && $error['name'] === 'blank') : ?>
					<p class="error">* ユーザー名を入力してください</p>
				<?php endif; ?>
			</dd>

			<dt>メールアドレス<span class="required">必須</span></dt>
			<dd>
				<input type="text" name="email" maxlength="255" value="<?php echo h($form['email']); ?>" />
				<?php if (isset($error['email']) && $error['email'] === 'blank') : ?>
					<p class="error">* メールアドレスを入力してください</p>
				<?php endif; ?>
				<?php if (isset($error['email']) && $error['email'] === 'duplicate') : ?>
					<p class="error">
						* 指定されたメールアドレスは<br>
						すでに登録されています
					</p>
				<?php endif; ?>
			</dd>

			<dt>パスワード<span class="required">必須</span></dt>
			<dd>
				<input type="password" name="password" maxlength="20" value="" />
				<?php if (isset($error['password']) && $error['password'] === 'blank') : ?>
					<p class="error">* パスワードを入力してください</p>
				<?php endif ?>
				<?php if (isset($error['password']) && $error['password'] === 'length') : ?>
					<p class="error">* パスワードは8文字以上で入力してください</p>
				<?php endif; ?>
			</dd>

			<dt>
				登録する２人のニックネームを<br>
				入力してください<span class="required">必須</span>
			</dt>
			<dd class="select-gender">
				<label>
					<i class="bi bi-gender-male text-primary"></i>
					<input type="hidden" name="male_num" value="1">
					<input type="text" name="male_name" maxlength="10" value="<?php echo h($form['male_name']); ?>">
				</label>
				<label>
					<i class="bi bi-gender-female text-danger"></i>
					<input type="hidden" name="female_num" value="2">
					<input type="text" name="female_name" maxlength="10" value="<?php echo h($form['female_name']); ?>">
				</label>
				<?php if (isset($error['couple']) && $error['couple'] === 'blank') : ?>
					<p class="error">* ２人のニックネームを入力してください</p>
				<?php endif; ?>
			</dd>
		</dl>
		<button class="btn btn-outline-success">入力内容を確認する</button>
	</form>
</section>

<!-- footer -->
<?php require_once(__DIR__ . '/../../app/_parts/_footer.php');
