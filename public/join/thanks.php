<?php
require(__DIR__ . '/../../app/config.php');

// header
require_once(__DIR__ . '/../../app/_parts/_header.php');
?>

<h2 class="section-title">会員登録が完了しました</h2>
<section class="check-complete">
	<a href="<?php echo LOGIN_URL; ?>" class="btn btn-outline-success">ログインする</a>
</section>

<!-- footer -->
<?php require_once(__DIR__ . '/../../app/_parts/_footer.php');
