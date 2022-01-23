<?php

namespace App\Repository;

use App\Mapper\UserMapper;

class UserRepository
{
    public UserMapper $mapper;
    public function __construct()
    {
        $this->mapper = new UserMapper();
    }

    public function iniciarSesion($userName, $pass){
        $this->mapper->findOne($userName, $pass);
    }

    public function registrarse($userName, $pass){
        $this->mapper->insert($userName, $pass);
    }
}