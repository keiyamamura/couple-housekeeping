<?php
require(__DIR__ . '/../../app/config.php');

use App\Database;
use App\Token;
use App\Register;
use App\Utils;

Token::create();

$error = [];
$email = '';
$password = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	Token::validate();

	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	if ($email === '' || $password === '') {
		$error['login'] = 'blank';
	} else {
		// ログインチェック
		$pdo = Database::getInstance();
		$info = new Register($pdo, $email);
		$register_info = $info->collateEmail();

		if ($register_info) {
			if (password_verify($password, $register_info->hash)) {
				// ログイン成功
				session_regenerate_id();
				$_SESSION['id'] = $register_info->id;
				$_SESSION['name'] = $register_info->name;
				header('Location: ' . MAIN_URL);
				exit();
			} else {
				$error['login'] = 'failed';
			}
		} else {
			$error['login'] = 'failed';
		}
	}
}

// header
require_once(__DIR__ . '/../../app/_parts/_header.php');
?>

<h2 class="section-title">
	メールアドレスとパスワードを記入してログインしてください。<br>
	会員登録がまだの方はこちらからどうぞ。<br>
	&raquo; <a href="<?php echo JOIN_URL; ?>" class="btn btn-outline-success">会員登録をする</a> &laquo;
</h2>
<section class="login">
	<form action="" method="post">
		<input type="hidden" name="token" value="<?php echo Utils::h($_SESSION['token']); ?>">

		<dl class="register-list">
			<dt>メールアドレス</dt>
			<dd>
				<input type="text" name="email" maxlength="255" value="<?php echo Utils::h($email); ?>" />
				<?php if (isset($error['login']) && $error['login'] === 'blank') : ?>
					<p class="error">* メールアドレスとパスワードをご記入ください</p>
				<?php endif; ?>
				<?php if (isset($error['login']) && $error['login'] === 'failed') : ?>
					<p class="error">* ログインに失敗しました。正しくご記入ください</p>
				<?php endif; ?>
			</dd>

			<dt>パスワード</dt>
			<dd>
				<input type="password" name="password" maxlength="20" value="" />
			</dd>
		</dl>
		<input type="submit" class="btn btn-outline-success" value="ログインする">
	</form>
</section>

<!-- footer -->
<?php require_once(__DIR__ . '/../../app/_parts/_footer.php');
