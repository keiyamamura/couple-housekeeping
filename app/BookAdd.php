<?php

namespace App;

require_once(__DIR__ . '/Book.php');

class BookAdd extends Book
{
	private $price;
	private $memo;

	public function __construct($pdo, $item_id, $member_id, $date, $price, $memo)
	{
		parent::__construct($pdo, $item_id, $member_id, $date);
		$this->price = $price;
		$this->memo = $memo;
	}

	public function add()
	{
		$stmt = $this->pdo->prepare(
			"INSERT INTO books (member_id, item_id, purchase_date, purchase_price, memo)
       VALUES (:member_id, :item_id, :date, :price, :memo)"
		);
		$stmt->bindValue('member_id', $this->member_id, \PDO::PARAM_INT);
		$stmt->bindValue('item_id', $this->item_id, \PDO::PARAM_INT);
		$stmt->bindValue('date', $this->date, \PDO::PARAM_STR);
		$stmt->bindValue('price', $this->price, \PDO::PARAM_INT);
		$stmt->bindValue('memo', $this->memo, \PDO::PARAM_STR);
		$result = $stmt->execute();

		$stmt = null;

		return $result;
	}
}
