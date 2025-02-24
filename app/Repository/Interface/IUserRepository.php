<?php
namespace App\Repository\Interface;

use App\DTO\UserDTO;

interface IUserRepository 
{
    public function register(UserDTO $userDTO);
    public function login(UserDTO $userDTO);
}
?>