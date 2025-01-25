<?php
namespace App\Tests\ControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Roles;
use App\Entity\Cohortes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SupervisorControllerTest extends WebTestCase
{
    private $entityManager;
    private $passwordHasher;
    private $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $container = $this->client->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);

        // Supprimer l'utilisateur de test s'il existe déjà
        $this->deleteTestUser();

        // Vérifier si le rôle "Superviseur" existe, sinon le créer
        $role = $this->entityManager->getRepository(Roles::class)->findOneBy(['name' => 'Superviseur']);
        if (!$role) {
            $role = new Roles();
            $role->setName('Superviseur');
            $this->entityManager->persist($role);
            $this->entityManager->flush();
        }

        // Créer un utilisateur de test avec le rôle de superviseur
        $user = new User();
        $user->setFirstName('Supervisor');
        $user->setLastName('User');
        $user->setEmail('supervisor@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setHasRole($role);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Authentifier l'utilisateur de test
        $this->authenticateTestUser();
    }

    private function deleteTestUser(): void
    {
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'supervisor@example.com']);
        if ($existingUser) {
            $this->entityManager->remove($existingUser);
            $this->entityManager->flush();
        }
    }

    private function authenticateTestUser(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'email' => 'supervisor@example.com',
                'password' => 'password123',
            ])
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful('Échec de l\'authentification de l\'utilisateur.');

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data, 'Token non trouvé dans la réponse.');

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
    }

    public function testAddTemporaryCohort(): void
    {
        $payload = [
            'name' => 'Temporary Cohort',
            'start_date' => '2025-01-25 10:00:00',
            'end_date' => '2025-02-25 10:00:00',
            'temporary' => true
        ];

        $jsonPayload = json_encode($payload);
        $this->assertNotFalse($jsonPayload, 'Échec de l\'encodage JSON.');

        $this->client->request(
            'POST',
            '/api/cohortes', // Assurez-vous que c'est la bonne route
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json'
            ],
            $jsonPayload
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), 'La cohorte temporaire n\'a pas été créée avec succès.');

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data, 'L\'ID de la cohorte est manquant dans la réponse.');

        $cohort = $this->entityManager->getRepository(Cohortes::class)->findOneBy(['name' => 'Temporary Cohort']);
        $this->assertNotNull($cohort, 'La cohorte temporaire n\'a pas été ajoutée à la base de données.');
        $this->assertEquals('2025-01-25 10:00:00', $cohort->getStartDate()->format('Y-m-d H:i:s'), 'La date de début ne correspond pas.');
        $this->assertEquals('2025-02-25 10:00:00', $cohort->getEndDate()->format('Y-m-d H:i:s'), 'La date de fin ne correspond pas.');
    }


    protected function tearDown(): void
    {
        // Supprimer l'utilisateur et les cohortes de test après chaque test
        $this->deleteTestUser();

        $cohort = $this->entityManager->getRepository(Cohortes::class)->findOneBy(['name' => 'Temporary Cohort']);
        if ($cohort) {
            $this->entityManager->remove($cohort);
            $this->entityManager->flush();
        }

        parent::tearDown();

        // Fermer l'EntityManager pour éviter les fuites de mémoire
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
