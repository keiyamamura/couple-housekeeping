<?php

namespace App;

class Info
{
	protected $pdo;

	public function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	public function showItemsData()
	{
		$stmt = $this->pdo->query('SELECT id, name FROM items');
		$items = $stmt->fetchAll();
		return $items;
	}
}
