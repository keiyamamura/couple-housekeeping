<?php
require(__DIR__ . '/../app/config.php');

use App\BookAdd;
use App\Database;
use App\InfoId;
use App\Member;
use App\Route;
use App\Token;
use App\Utils;

Token::create();
$pdo = Database::getInstance();

if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
	$get_id = ($_SESSION['id']);
	$get_name = ($_SESSION['name']);

	// maleデータ と femaleデータの仕分け
	$get_gender_num = (int) filter_input(INPUT_GET, 'gender', FILTER_SANITIZE_NUMBER_INT);
	if (!$get_gender_num || $get_gender_num >= 3) {
		header('Location: ' . MAIN_URL);
	}
	$member_info = new Member($pdo, $get_id, $get_gender_num);
	$member = $member_info->getData();

	// 項目データの取得
	$get_item_id = filter_input(INPUT_GET, 'item', FILTER_SANITIZE_NUMBER_INT);
	if (!$get_item_id || $get_item_id >= 4) {
		header('Location: ' . MAIN_URL);
	}
	$item = new InfoId($pdo, $get_item_id);
	$item = $item->getItemData();
} else {
	header('Location: ' . LOGIN_URL);
	exit();
}

$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	Token::validate();

	$purchase_date = filter_input(INPUT_POST, 'purchase_date', FILTER_SANITIZE_NUMBER_INT);
	if ($purchase_date === '') {
		$error['purchase_date'] = 'blank';
	}
	$purchase_price = filter_input(INPUT_POST, 'purchase_price', FILTER_SANITIZE_NUMBER_INT);
	if ($purchase_price === '') {
		$error['price'] = 'blank';
	}
	$memo = filter_input(INPUT_POST, 'memo', FILTER_SANITIZE_STRING);
	if (!$memo) {
		$memo = '';
	}

	if (empty($error)) {
		$book = new BookAdd($pdo, $item->id, $member->id, $purchase_date, $purchase_price, $memo);
		$result = $book->add();

		if ($result) {
			$_SESSION['success_message'] = '登録が完了しました';
		} else {
			$error['message'] = 'blank';
		}

		$url = new Route($item->id, $member->gender);
		define('ADD_URL', $url->getAddPageUrl());
		header("Location: " . ADD_URL);
		exit();
	}
}
// header
require_once(__DIR__ . '/../app/_parts/_header.php');
?>

<section id="add_page">
	<h3 class="amount"><span><?php echo Utils::h($member->name); ?>(<?php echo Utils::h($item->name); ?>)</span>追加</h3>
	<?php if (!empty($_SESSION['success_message'])) : ?>
		<p class="success_message">
			<?php echo Utils::h($_SESSION['success_message']); ?>
			<?php unset($_SESSION['success_message']); ?>
		</p>
	<?php endif; ?>
	<form action="" method="post" >
		<input type="hidden" name="token" value="<?php echo Utils::h($_SESSION['token']); ?>">
		<ul class="book-register">
			<li>
				<label>
					日付：
					<input type="date" name="purchase_date" data-type="date" value="">
					<p class="error hide" id="error_date">* 日付を入力してください</p>
				</label>
			</li>
			<li>
				<label>
					金額：
					<input type="number" step="1" min="1" name="purchase_price" data-type="price" value="">
					<p class="error hide" id="error_price">* 金額を入力してください</p>
				</label>
			</li>
			<li>
				<label>
					メモ：
					<input type="text" name="memo" maxlength="10" data-type="memo" value="">
				</label>
			</li>
		</ul>
		<a href="index.php" class="btn btn-outline-danger">一覧へ戻る</a>
		<!-- Button trigger modal -->
		<button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
			追加する
		</button>

		<!-- Modal -->
		<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="staticBackdropLabel">こちらで登録しますか？</h5>
					</div>
					<div class="modal-body">
						<dl>
							<dt>日付：</dt>
							<dd id="date">

							</dd>
							<dt>金額：</dt>
							<dd id="price"></dd>
							<dt>メモ：</dt>
							<dd id="memo"></dd>
						</dl>
					</div>
					<div class="modal-footer m-auto">
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal">やり直す</button>
						<button id="add_button" type="submit" class="btn btn-success">追加する</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>
<!-- /#add_page -->

<!-- footer -->
<?php require_once(__DIR__ . '/../app/_parts/_footer.php'); ?>
