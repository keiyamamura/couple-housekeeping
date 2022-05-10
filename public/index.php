<?php
require(__DIR__ . '/../app/config.php');

use App\Book;
use App\Calendar;
use App\Database;
use App\Info;
use App\Member;
use App\Utils;

$pdo = Database::getInstance();

if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
	$login_user_id = $_SESSION['id'];
	$login_user_name = $_SESSION['name'];

	// memberの取得
	$member_info = [];
	$member_info[0] = new Member($pdo, $login_user_id, 1);
	$member_info[1] = new Member($pdo, $login_user_id, 2);
	$male = $member_info[0]->getData();
	$female = $member_info[1]->getData();

	// 項目データの取得
	$items = new Info($pdo);
	$items = $items->showItemsData();

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
		//クリック値がない場合
		if (isset($_GET['action']) && $_GET['action'] === 'calender' && isset($_SESSION['date'])) {
			$get_date = $_SESSION['date'];
			$date = mktime(0, 0, 0, 1, $get_date['month'], $get_date['year']); //本日（YYYY-MM-DDの形）
			$get_y = $get_date['year']; //本日の年
			$get_m = $get_date['month']; //本日の月
		} else {
			$date = date('Y-m-d'); //本日（YYYY-MM-DDの形）
			$get_y = date('Y'); //本日の年
			$get_m = date('m'); //本日の月
		}
	}

	//カレンダー
	$Calendar = new Calendar($get_y, $get_m, $date, Utils::h($_SESSION['token']));

	// 日付の送信
	Utils::sendDate($get_y, $get_m);

	// 日付の結合
	$date_check = sprintf('%s-%s', $get_y, $get_m) . '%';

	$male_subtotal_amount = 0;
	$female_subtotal_amount = 0;
} else {
	header('Location: ' . LOGIN_URL);
	exit();
}

// header
require_once(__DIR__ . '/../app/_parts/_header.php');
?>

<h2 class="date">
	<?php Utils::h($Calendar->getRsv()); ?>
</h2>

<section id="main_page">
	<div class="row">
		<div id="male" class="col-md-6">
			<h3 class="amount"><span><?php echo Utils::h($male->name); ?></span> の支払額</h3>
			<table class="table table-borderless">
				<tbody>
					<?php foreach ($items as $item) : ?>
						<tr>
							<th scope="row">
								<a href="show.php?item=<?php echo Utils::h($item->id); ?>&gender=<?php echo Utils::h($male->gender); ?>" class="category btn btn-outline-dark">
									<?php echo Utils::h($item->name); ?>
								</a>
							</th>
							<td>
								<?php
								$male_purchase_price = new Book($pdo, $item->id, $male->id, $date_check);
								echo Utils::h(Utils::num($male_purchase_price->getPurchasePrice()));
								$male_subtotal_amount += $male_purchase_price->getPurchasePrice();
								?>円
							</td>
							<td>
								<a href="add.php?item=<?php echo Utils::h($item->id); ?>&gender=<?php echo Utils::h($male->gender); ?>" class="btn btn-outline-success">追加</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="row">小計</th>
						<td>
							<?php echo  Utils::h(Utils::num($male_subtotal_amount)); ?>円</td>
					</tr>
				</tfoot>
			</table>
		</div>

		<div id="famale" class="col-md-6">
			<h3 class="amount"><span><?php echo Utils::h($female->name); ?></span> の支払額</h3>
			<table class="table table-borderless">
				<tbody>
					<?php foreach ($items as $item) : ?>
						<tr>
							<th scope="row">
								<a href="show.php?item=<?php echo Utils::h($item->id); ?>&gender=<?php echo Utils::h($female->gender); ?>" class="category btn btn-outline-dark">
									<?php echo Utils::h($item->name); ?>
								</a>
							</th>
							<td>
								<?php
								$female_purchase_price = new Book($pdo, $item->id, $female->id, $date_check);
								echo Utils::h(Utils::num($female_purchase_price->getPurchasePrice()));
								$female_subtotal_amount += $female_purchase_price->getPurchasePrice();
								?>円
							</td>
							<td>
								<a href="add.php?item=<?php echo Utils::h($item->id); ?>&gender=<?php echo Utils::h($female->gender); ?>" class="btn btn-outline-success">追加</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="row">小計</th>
						<td><?php echo  Utils::h(Utils::num($female_subtotal_amount)); ?>円</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="total">
		<div class="total-amount">
			<p>２人の合計金額</p>
			<span><?php Utils::totalAmount($male_subtotal_amount, $female_subtotal_amount); ?></span> 円
		</div>
		<div class="total-amount">
			<p>差額</p>
			<span><?php Utils::removeDifference($male_subtotal_amount, $female_subtotal_amount); ?></span> 円
		</div>
		<div class="total-amount">
			<p>小計の少ない人が、<br>
				支払う額</p>
			<span><?php Utils::perPersonAmount($male_subtotal_amount, $female_subtotal_amount); ?></span> 円
		</div>
	</div>
</section>
<!-- /#main_page -->

<!-- footer -->
<?php require_once(__DIR__ . '/../app/_parts/_footer.php'); ?>
