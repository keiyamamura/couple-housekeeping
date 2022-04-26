<?php

namespace App;

class Member
{
	private $pdo;
	private $id;
	private $gender;

	public function __construct($pdo, $id, $gender)
	{
		$this->pdo = $pdo;
		$this->id = $id;
		$this->gender = $gender;
	}

	public function getData()
	{
		$stmt = $this->pdo->prepare("SELECT id, gender, name FROM members WHERE user_id = :id AND gender = :gender");

		$stmt->bindValue('id', $this->id, \PDO::PARAM_INT);
		$stmt->bindValue('gender', $this->gender, \PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch();

		return $result;
	}
}
