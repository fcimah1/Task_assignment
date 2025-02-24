<?php
namespace App\Repository;

use App\DTO\UserDTO;
use App\Models\User;
use App\Repository\Interface\IUserRepository;
use Illuminate\Support\Facades\Hash;

class UserRepository implements IUserRepository
{
    public function register(UserDTO $userDTO)
    {
        return User::create($userDTO->toArray());
        
    }

    public function login( $userDTO)
    {
        dd($userDTO);
        $user = User::where('email', $userDTO->email)->first();
        if (!$user && !Hash::check($user->password, $userDTO->password)) {
            return 'Invalid email or password';
        }else{
            return $user;
        }
    }
}
