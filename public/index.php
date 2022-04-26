<?php
require(__DIR__ . '/../app/config.php');

$pdo = getPdoInstance();

if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
	$get_id = ($_SESSION['id']);
	$get_name = ($_SESSION['name']);

	// maleデータ と femaleデータの取得
	$members_data = [];
	for ($i = 1; $i <= 2; $i++) {
		$members_data[] = getMembersData($pdo, $get_id, $i);
	}
	$male = $members_data[0];
	$female = $members_data[1];

	// 項目データの取得
	$items = getItems($pdo);

	//カレンダー

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

	$date_check = sprintf('%s-%s', $get_y, $get_m) . '%';

	$send_date = [];
	$send_date['year'] = $get_y;
	$send_date['month'] = $get_m;
	$_SESSION['date'] = $send_date;
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
	<?php h(get_rsv_calendar($get_y, $get_m, $date, h($_SESSION['token']))); ?>
</h2>

<section id="main_page">
	<div class="row">
		<div id="male" class="col-sm-6">
			<h3 class="amount"><span><?php echo h($male->name); ?></span> の支払額</h3>
			<table class="table table-borderless">
				<tbody>
					<?php foreach ($items as $item) : ?>
						<tr>
							<th scope="row">
								<a href="show.php?item=<?php echo h($item->id); ?>&gender=<?php echo h($male->gender); ?>" class="category btn btn-outline-dark">
									<?php echo h($item->name); ?>
								</a>
							</th>
							<td>
								<?php
								$male_purchase_price = getBooksPurchasePrice($pdo, $item->id, $male->id, $date_check);
								echo h(num($male_purchase_price));
								$male_subtotal_amount += $male_purchase_price
								?>円
							</td>
							<td>
								<a href="add.php?item=<?php echo h($item->id); ?>&gender=<?php echo h($male->gender); ?>" class="btn btn-outline-success">追加</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="row">小計</th>
						<td>
							<?php echo h(num($male_subtotal_amount)); ?>円</td>
					</tr>
				</tfoot>
			</table>
		</div>

		<div id="famale" class="col-sm-6">
			<h3 class="amount"><span><?php echo h($female->name); ?></span> の支払額</h3>
			<table class="table table-borderless">
				<tbody>
					<?php foreach ($items as $item) : ?>
						<tr>
							<th scope="row">
								<a href="show.php?item=<?php echo h($item->id); ?>&gender=<?php echo h($female->gender); ?>" class="category btn btn-outline-dark">
									<?php echo h($item->name); ?>
								</a>
							</th>
							<td>
								<?php
								$female_purchase_price = getBooksPurchasePrice($pdo, $item->id, $female->id, $date_check);
								echo h(num($female_purchase_price));

								$female_subtotal_amount += $female_purchase_price;
								?>円
							</td>
							<td>
								<a href="add.php?item=<?php echo h($item->id); ?>&gender=<?php echo h($female->gender); ?>" class="btn btn-outline-success">追加</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="row">小計</th>
						<td><?php echo h(num($female_subtotal_amount)); ?>円</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div class="total">
		<div class="total-amount">
			<p>２人の合計金額</p>
			<span><?php totalAmount($male_subtotal_amount, $female_subtotal_amount); ?></span> 円
		</div>
		<div class="total-amount">
			<p>差額</p>
			<span><?php removeDifference($male_subtotal_amount, $female_subtotal_amount); ?></span> 円
		</div>
		<div class="total-amount">
			<p>小計の少ない人が、<br>
				多い人へ支払う額</p>
			<span><?php perPersonAmount($male_subtotal_amount, $female_subtotal_amount); ?></span> 円
		</div>
	</div>
</section>
<!-- /#main_page -->

<!-- footer -->
<?php require_once(__DIR__ . '/../app/_parts/_footer.php'); ?>
