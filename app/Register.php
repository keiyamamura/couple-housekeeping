<?php

namespace App;

class Register
{
    private $pdo;
    private $form;

    public function __construct($pdo, $form)
    {
        $this->pdo = $pdo;
        $this->form = $form;
    }

    public function userCreate()
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name, email, password)
      VALUES (:name, :email, :password)"
        );
        $password = password_hash($this->form['password'], PASSWORD_DEFAULT);

        $stmt->bindValue('name', $this->form['name'], \PDO::PARAM_STR);
        $stmt->bindValue('email', $this->form['email'], \PDO::PARAM_STR);
        $stmt->bindValue('password', $password, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function memberCreate()
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO members (user_id, gender, name)
      VALUES (:user_id, :gender, :member_name)"
        );

        $id = $this->pdo->lastInsertId();
        $gender_num = $this->form['male_num'];
        $name = $this->form['male_name'];

        $stmt->bindParam('user_id', $id, \PDO::PARAM_INT);
        $stmt->bindParam('gender', $gender_num, \PDO::PARAM_INT);
        $stmt->bindParam('member_name', $name, \PDO::PARAM_STR);
        $stmt->execute();

        $gender_num = $this->form['female_num'];
        $name = $this->form['female_name'];
        $stmt->execute();
    }

    public function checkEmail()
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS email FROM users WHERE email = :email");
        $stmt->bindValue('email', $this->form, \PDO::PARAM_STR);
        $stmt->execute();

        $count = $stmt->fetch();
        if ($count->email > 0) {
            return 'duplicate';
        }
    }

    public function collateEmail()
    {
        $stmt = $this->pdo->prepare('SELECT id, name, password AS hash FROM users WHERE email = :email LIMIT 1');
        $stmt->bindValue('email', $this->form, \PDO::PARAM_STR);
        $stmt->execute();
        $user_data = $stmt->fetch();

        return $user_data;
    }
}
