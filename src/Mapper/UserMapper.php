<?php

namespace App\Mapper;

use App\FlashMessage;
use App\Registry;
use Exception;
use PDOException;

class UserMapper
{
    public function __construct()
    {
        $this->pdo = Registry::get("PDO");
    }

    public function findOne($userName, $pass)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * from user WHERE username = :username');
            $stmt->bindParam(':username', $userName);
            $stmt->execute();
            $row = $stmt->fetch();
            FlashMessage::set("row", $row);
        } catch (PDOException $e) {
            echo "fail";
        }
    }

    public function insert($userName, $pass){
        try {
            $pdo = Registry::get(Registry::PDO);
            $pdo->beginTransaction();
            $this->findOne($userName, $pass);
            $existsRate = FlashMessage::get("row");
            if($existsRate == false){
                $sql2 = 'INSERT INTO user (username, password) VALUES(?, ?)';
                $pdo->prepare($sql2)->execute([$userName, $pass]);
                $pdo->commit();
            }else{
                FlashMessage::set("errors", "Ya hay un usuario con ese mismo nombre y contraseña");
                $pdo->commit();
            }
        }catch (Exception $e){
            $pdo->rollBack();
        }
    }
}