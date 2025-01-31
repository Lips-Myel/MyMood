<?php

namespace App\DataFixtures;

use App\Entity\CallAlerts;
use App\Entity\User;
use App\Entity\Mood;
use App\Entity\Roles;
use App\Entity\Cohortes;
use App\Entity\HistoricalMood;
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
        $roleNames = ['Étudiant', 'Superviseur', 'Administrateur'];
        foreach ($roleNames as $roleName) {
            $role = new Roles();
            $role->setName($roleName);
            $manager->persist($role);
            $roles[] = $role;
        }

        // Créer 3 cohortes (front, back, projets temporaires)
        $cohorts = [];
        $cohortNames = ['Front', 'Back', 'Projets Temporaires'];
        foreach ($cohortNames as $cohortName) {
            $cohort = new Cohortes();
            $cohort->setName($cohortName);
            $cohort->setStartDate($faker->dateTimeThisYear());
            $cohort->setEndDate($faker->dateTimeThisYear()->modify('+6 months'));
            $cohort->setTemporary($cohortName === 'Projets Temporaires');
            $manager->persist($cohort);
            $cohorts[] = $cohort;
        }

        // Créer 20 utilisateurs
        $users = [];
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setEmail($faker->unique()->safeEmail());
            $user->setBlackList(false);
            $user->setLastConnection($faker->dateTimeThisMonth());
            $user->setHasMood($moods[array_rand($moods)]); // Assigner un mood aléatoire
            $user->setHasRole($roles[array_rand($roles)]); // Assigner un rôle aléatoire

            // Assigner une cohorte aléatoire à l'utilisateur
            $user->addCohort($cohorts[array_rand($cohorts)]); // Assigner une cohorte aléatoire

            // Hacher le mot de passe
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                'mots2passe2025'
            ));
            $manager->persist($user);
            $users[] = $user;
        }

        // Créer un historique de 10 moods différents pour chaque utilisateur
        foreach ($users as $user) {
            for ($i = 1; $i <= 10; $i++) {
                $historicalMood = new HistoricalMood($user, $moods[array_rand($moods)]);
                $historicalMood->setDate($faker->dateTimeThisYear());
                $historicalMood->setScore($faker->numberBetween(1, 100));
                $manager->persist($historicalMood);
            }
        }

         // Créer des alertes d'appel pour certains utilisateurs
         for ($i = 0; $i < 5; $i++) { // 5 appels aléatoires
            $callAlert = new CallAlerts();
            $callAlert->setCallStatut($faker->boolean());
            $callAlert->setCallDate($faker->dateTimeThisMonth());
            $callAlert->setAlertBetween($users[array_rand($users)]); // Assigner un utilisateur aléatoire

            $manager->persist($callAlert);
        }

        $manager->flush();
    }
}