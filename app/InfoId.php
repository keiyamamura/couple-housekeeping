<?php

namespace App;

require_once(__DIR__ . '/Info.php');

class InfoId extends Info
{
	private $id;

	public function __construct($pdo, $id)
	{
		parent::__construct($pdo);
		$this->id = $id;
	}

	public function getItemData()
	{
		$stmt = $this->pdo->prepare('SELECT id, name FROM items WHERE id = :id');
		$stmt->bindValue('id', $this->id, \PDO::PARAM_INT);
		$stmt->execute();
		$item = $stmt->fetch();
		return $item;
	}

	public function deleteBook()
	{
		$stmt = $this->pdo->prepare('DELETE FROM books WHERE id = :id LIMIT 1');
		$stmt->bindValue('id', $this->id, \PDO::PARAM_INT);
		$stmt->execute();
	}
}
