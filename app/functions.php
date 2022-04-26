<?php

// htmlspecialcharsを短くする
function h($str)
{
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 現在ページのフルURLを返す
function get_current_page_url()
{
	return (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function addUrl($item_id, $member_gender)
{
	return "http://localhost:8888/portfolio/public/add.php?item=$item_id&gender=$member_gender";
}

function showUrl($get_item_id, $get_gender_num)
{
	return "http://localhost:8888/portfolio/public/show.php?item=$get_item_id&gender=$get_gender_num";
}

function num($num)
{
	return number_format($num);
}

function createToken()
{
	if (!isset($_SESSION['token'])) {
		$_SESSION['token'] = bin2hex(random_bytes(32));
	}
}

function validateToken()
{
	if (empty($_SESSION['token']) || $_SESSION['token'] !== filter_input(INPUT_POST, 'token')) {
		exit('Invalid post request');
	}
}

function getPdoInstance()
{
	try {
		$pdo = new PDO(
			DSN,
			DB_USER,
			DB_PASS,
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
				PDO::ATTR_EMULATE_PREPARES => false,
			],
		);

		return $pdo;
	} catch (PDOException $e) {
		echo $e->getMessage();
		exit;
	}
}

function createUser($pdo, $form)
{
	$stmt = $pdo->prepare(
		"INSERT INTO users (name, email, password)
     VALUES (:name, :email, :password)"
	);
	$password = password_hash($form['password'], PASSWORD_DEFAULT);

	$stmt->bindValue('name', $form['name'], PDO::PARAM_STR);
	$stmt->bindValue('email', $form['email'], PDO::PARAM_STR);
	$stmt->bindValue('password', $password, PDO::PARAM_STR);
	$stmt->execute();
}

function createMembers($pdo, $form)
{
	$stmt = $pdo->prepare(
		"INSERT INTO members (user_id, gender, name)
     VALUES (:user_id, :gender, :member_name)"
	);

	$user_id = $pdo->lastInsertId();
	$gender_num = $form['male_num'];
	$name = $form['male_name'];

	$stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
	$stmt->bindParam('gender', $gender_num, PDO::PARAM_INT);
	$stmt->bindParam('member_name', $name, PDO::PARAM_STR);
	$stmt->execute();

	$gender_num = $form['female_num'];
	$name = $form['female_name'];
	$stmt->execute();
}

function getMembersData($pdo, $get_id, $gender_num)
{
	$stmt = $pdo->prepare("SELECT id, gender, name FROM members WHERE user_id = :get_id AND gender = :gender_num");

	$stmt->bindParam('get_id', $get_id, PDO::PARAM_INT);
	$stmt->bindParam('gender_num', $gender_num, PDO::PARAM_INT);
	$stmt->execute();
	$member_data = $stmt->fetch();

	return $member_data;
}

function getItems($pdo)
{
	$stmt = $pdo->query('SELECT id, name FROM items');
	$items = $stmt->fetchAll();

	return $items;
}

function checkItemData($pdo, $id)
{
	$stmt = $pdo->prepare('SELECT id, name FROM items WHERE id = :id');
	$stmt->bindValue('id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$item = $stmt->fetch();

	return $item;
}

// カレンダー
function get_rsv_calendar($yyyy, $mm, $date, $token)
{
	$this_year = $yyyy; //年
	$this_month = $mm; //月
	$unixmonth = mktime(0, 0, 0, $this_month, 1, $this_year); //該当月1日のタイムスタンプ
	$prev = date('Y-m', strtotime('-1 month', $unixmonth)); //前月の算出
	$next = date('Y-m', strtotime('+1 month', $unixmonth)); //次月の算出

	$calendar_output = '<caption class="rsv_calendar">' . "\n\t" . '<form action="" method="post">';
	$calendar_output .= "\n\t\t" . '<input class="next" type="submit" name="calendar[' . $prev . '*' . $date . ']" value="&laquo;">';
	$calendar_output .= "\n\t\t" . $this_year . '年' . $this_month . '月';
	$calendar_output .= "\n\t\t" . '<input class="prev" type="submit" name="calendar[' . $next . '*' . $date . ']" value="&raquo;">';
	$calendar_output .= "\n\t\t" . '<input type="hidden" name="token" value="' . $token . '">';
	$calendar_output .= "\n\t</form>\n</caption>";

	echo $calendar_output; //出力
}

function getBooksPurchasePrice($pdo, $item_id, $member_id, $date_check)
{
	$stmt = $pdo->prepare(
		"SELECT sum(b.purchase_price) AS item_total
     FROM books b JOIN members m ON b.member_id = m.id JOIN items i ON b.item_id = i.id
     WHERE b.purchase_date LIKE :date_check AND m.id = :member_id AND i.id = :item_id
     GROUP BY i.name
    "
	);
	$stmt->bindValue('date_check', $date_check, PDO::PARAM_STR);
	$stmt->bindValue('member_id', $member_id, PDO::PARAM_INT);
	$stmt->bindValue('item_id', $item_id, PDO::PARAM_STR);
	$stmt->execute();
	$purchase_price = $stmt->fetchAll();

	if (empty($purchase_price)) {
		return $purchase_price = 0;
	} else {
		return $purchase_price[0]->item_total;
	}
}

function getBooksPurchaseData($pdo, $item_id, $member_id, $date_check)
{
	$stmt = $pdo->prepare(
		"SELECT b.id, purchase_date AS date, purchase_price AS price, memo
     FROM books b JOIN members m ON b.member_id = m.id JOIN items i ON b.item_id = i.id
     WHERE b.purchase_date LIKE :date_check AND m.id = :member_id AND i.id = :item_id
     ORDER BY b.id DESC
    "
	);
	$stmt->bindValue('date_check', $date_check, PDO::PARAM_STR);
	$stmt->bindValue('member_id', $member_id, PDO::PARAM_INT);
	$stmt->bindValue('item_id', $item_id, PDO::PARAM_STR);
	$stmt->execute();
	$purchase_data = $stmt->fetchAll();

	return $purchase_data;
}

function totalAmount($maleSubTotalAmount, $femaleSubTotalAmount)
{
	$totalAmount = $maleSubTotalAmount + $femaleSubTotalAmount;
	echo h(num($totalAmount));
}

function removeDifference($maleSubTotalAmount, $femaleSubTotalAmount)
{
	$difference = $maleSubTotalAmount - $femaleSubTotalAmount;
	$removeDifference = str_replace('-', '', $difference);
	echo h(num($removeDifference));
}

function  perPersonAmount($maleSubTotalAmount, $femaleSubTotalAmount)
{
	$perPersonAmount = ($maleSubTotalAmount - $femaleSubTotalAmount) / 2;
	$removePerPersonAmount = str_replace('-', '', $perPersonAmount);
	echo h(num($removePerPersonAmount));
}

function deleteBookData($pdo, $delete_id)
{
	$stmt = $pdo->prepare('DELETE FROM books WHERE id = :delete_id LIMIT 1');
	$stmt->bindValue('delete_id', $delete_id, PDO::PARAM_INT);
	$stmt->execute();
}
