<?php
namespace App\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class PasswordFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    // Générer un mot de passe aléatoire
    public function generateRandomPassword(int $length = 8): string
    {
        return bin2hex(random_bytes($length / 2));  // Crée un mot de passe de la bonne longueur
    }

    // Hacher un mot de passe
    public function hashPassword(string $plainPassword): string
    {
        $user = new User(); // L'encodeur nécessite un objet User
        return $this->passwordHasher->hashPassword($user, $plainPassword);
    }

    // Créer un mot de passe et le hacher
    public function createHashedPassword(int $length = 8): array
    {
        $plainPassword = $this->generateRandomPassword($length);
        $hashedPassword = $this->hashPassword($plainPassword);

        return ['plain' => $plainPassword, 'hashed' => $hashedPassword];
    }
}
