<?php
// src/Service/UserPasswordHasher.php
namespace App\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserPasswordHasher
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function hashPassword(User $user, string $plainPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
    }
}