<?php
require(__DIR__ . '/../../app/config.php');

$error = [];
$email = '';
$password = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($email === '' || $password === '') {
        $error['login'] = 'blank';
    } else {
        // ログインチェック
        $pdo = getPdoInstance();
        $stmt = $pdo->prepare('SELECT id, name, password AS hash FROM users WHERE email = :email LIMIT 1');
        $stmt->bindValue('email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $login_data = $stmt->fetch();
        if ($login_data) {
            if (password_verify($password, $login_data->hash)) {
                // ログイン成功
                session_regenerate_id();
                $_SESSION['id'] = $login_data->id;
                $_SESSION['name'] = $login_data->name;
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
    会員登録がまだの方はこちらからどうぞ。&raquo; <a href="<?php echo JOIN_URL; ?>" class="btn btn-outline-success">会員登録をする</a>
</h2>
<section class="login">
    <form action="" method="post">
        <dl class="register-list">
            <dt>メールアドレス</dt>
            <dd>
                <input type="text" name="email" maxlength="255" value="<?php echo h($email); ?>" />
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
