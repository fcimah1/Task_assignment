<?php
namespace App\DTO;
use Spatie\LaravelData\Data;
Class UserDTO extends Data
{
    public function __construct(public string $name, public string $email, public string $password)
    {
        $this->password = bcrypt($password);
    }
}
