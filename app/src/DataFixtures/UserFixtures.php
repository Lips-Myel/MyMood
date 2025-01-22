<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Mood;
use App\Entity\Roles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Faker en français

        // Créer 10 moods
        $moods = [];
        for ($i = 1; $i <= 10; $i++) {
            $mood = new Mood();
            $mood->setScore($faker->numberBetween(1, 100));
            $mood->setDateMood($faker->dateTimeThisYear());
            $manager->persist($mood);
            $moods[] = $mood;
        }

        // Créer 3 rôles possibles
        $roles = [];
        $roleNames = ['Étudiant', 'Superviseur', 'Administrateur']; // Définir les rôles
        foreach ($roleNames as $roleName) {
            $role = new Roles();
            $role->setName($roleName);
            $manager->persist($role);
            $roles[] = $role;
        }

        // Créer 20 utilisateurs
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setEmail($faker->unique()->safeEmail());
            $user->setBlackList(false);
            $user->setLastConnection($faker->dateTimeThisMonth());
            $user->setHasMood($moods[array_rand($moods)]); // Assigner un mood aléatoire
            $user->setHasRole($roles[array_rand($roles)]); // Assigner un rôle aléatoire
            
            // Hacher le mot de passe
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                'mots2passe2025'
            ));
            $manager->persist($user);
        }

        $manager->flush();
    }
}