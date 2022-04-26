<?php

namespace App;

class Book
{
	protected $pdo;
	protected $item_id;
	protected $member_id;
	protected $date;

	public function __construct($pdo, $item_id, $member_id, $date)
	{
			$this->pdo = $pdo;
			$this->item_id = $item_id;
			$this->member_id = $member_id;
			$this->date = $date;
	}

	private function fetchAll($stmt)
	{
			$stmt->bindValue('date', $this->date, \PDO::PARAM_STR);
			$stmt->bindValue('member_id', $this->member_id, \PDO::PARAM_INT);
			$stmt->bindValue('item_id', $this->item_id, \PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll();

			return $result;
	}

	public function getPurchasePrice()
	{
			$stmt = $this->pdo->prepare(
					"SELECT sum(b.purchase_price) AS item_total
		FROM books b JOIN members m ON b.member_id = m.id JOIN items i ON b.item_id = i.id
		WHERE b.purchase_date LIKE :date AND m.id = :member_id AND i.id = :item_id
		GROUP BY i.name
		"
			);
			$purchase_price = $this->fetchAll($stmt);

			if (empty($purchase_price)) {
					return $purchase_price = 0;
			} else {
					return $purchase_price[0]->item_total;
			}
	}

	public function getPurchaseData()
	{
		$stmt = $this->pdo->prepare(
			"SELECT b.id, purchase_date AS date, purchase_price AS price, memo
			FROM books b JOIN members m ON b.member_id = m.id JOIN items i ON b.item_id = i.id
			WHERE b.purchase_date LIKE :date AND m.id = :member_id AND i.id = :item_id
			ORDER BY b.id DESC
			");
			$purchase_data = $this->fetchAll($stmt);

			return $purchase_data;
	}
}
