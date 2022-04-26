<?php
require(__DIR__ . '/../app/config.php');

use App\Book;
use App\Calendar;
use App\Database;
use App\InfoId;
use App\Member;
use App\Route;
use App\Token;
use App\Utils;

$pdo = Database::getInstance();
Token::create();

if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
	$get_id = ($_SESSION['id']);
	$get_name = ($_SESSION['name']);

	// maleデータ と femaleデータの仕分け
	$get_gender_num = filter_input(INPUT_GET, 'gender', FILTER_SANITIZE_NUMBER_INT);
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

	//日付情報を取得
	if (isset($_POST['calendar'])) {
		//クリック値がある場合
		$tmp = !is_string(key($_POST['calendar'])) ? '' : key($_POST['calendar']);
		if (strpos($tmp, '*') !== false) {
			//前後月の遷移ボタンがクリックされた場合
			$date = substr($tmp, 8, 10); //保持されている日付
			$get_y = substr($tmp, 0, 4); //年
			$get_m = substr($tmp, 5, 2); //月
		}
	} else {
		//index.phpから送られてきた日付を取得
		$get_date = $_SESSION['date'];
		$date = mktime(0, 0, 0, 1, $get_date['month'], $get_date['year']); //本日（YYYY-MM-DDの形）
		$get_y = $get_date['year']; //本日の年
		$get_m = $get_date['month']; //本日の月
	}


	//カレンダー
	$Calendar = new Calendar($get_y, $get_m, $date, Utils::h($_SESSION['token']));

	// 日付の送信
	Utils::sendDate($get_y, $get_m);

	// 日付の結合
	$date_check = sprintf('%s-%s', $get_y, $get_m) . '%';
	// 家計簿データの取得
	$book_info = new Book($pdo, $item->id, $member->id, $date_check);
} else {
	header('Location: ' . LOGIN_URL);
	exit();
}

$url = new Route($get_item_id, $get_gender_num);
if (empty($url->getShowPageUrl())) {
	return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	Token::validate();

	$delete_id = filter_input(INPUT_POST, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
	if ($delete_id) {
		$book_info = new InfoId($pdo, $delete_id);
		$book_info->deleteBook();

		header("Location: " . $url->getShowPageUrl());
		exit();
	}
}

// header
require_once(__DIR__ . '/../app/_parts/_header.php');
?>

<h2 class="date">
	<?php Utils::h($Calendar->getRsv()); ?>
</h2>

<section id="show_page">
	<div>
		<h3 class="amount"><span><?php echo Utils::h($member->name); ?>（<?php echo Utils::h($item->name); ?>）</span>の詳細</h3>
		<table class="table table-hover">
			<thead>
				<tr>
					<th scope="col">日付</th>
					<th scope="col">購入金額</th>
					<th scope="col">メモ</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($book_info->getPurchaseData() as $book) :
					// 年月日の切り分け
					$take_year = substr($book->date, 0, 4);
					$take_month = substr($book->date, 5, 2);
					$take_date = substr($book->date, 8);
					//指定日の曜日を取得する
					$timestamp = mktime(0, 0, 0, $take_date, $take_month, $take_year);
					$week_num = date('w', $timestamp);
					//配列を使用し、要素順に(日〜土)を設定
					$week_jp = ['日', '月', '火', '水', '木', '金', '土'];
					$take_week = $week_jp[$week_num];
					// 月日(曜日)の設定
					$showDate = sprintf('%d/%d(%s)', $take_month, $take_date, $take_week);
				?>
					<tr>
						<td scope="row"><?php echo Utils::h($showDate); ?></td>
						<td><?php echo Utils::h(Utils::num($book->price)); ?>円</td>
						<td class="memo"><?php echo Utils::h($book->memo); ?></td>
						<td>
							<form action="" method="post">
								<span class="btn btn-outline-danger delete"><i class="bi bi-trash"></i></span>
								<input type="hidden" name="token" value="<?php echo Utils::h($_SESSION['token']); ?>">
								<input type="hidden" name="delete_id" value="<?php echo Utils::h($book->id); ?>">
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<div class="total">
		<div class="total-amount">
			<p>合計金額</p>
			<span><?php echo Utils::h(Utils::num($book_info->getPurchasePrice())); ?></span> 円
		</div>
	</div>

	<a href="index.php?action=calender" class="detail btn btn-outline-success">一覧へ戻る</a>
</section>
<!-- /#show-page -->

<!-- footer -->
<?php require_once(__DIR__ . '/../app/_parts/_footer.php'); ?>
