<?php
namespace App\Tests\ControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Entity\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    private $entityManager;
    private $passwordHasher;
    private $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->passwordHasher = $this->client->getContainer()->get(UserPasswordHasherInterface::class);

        // Supprimer l'utilisateur de test s'il existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
        if ($existingUser) {
            $this->entityManager->remove($existingUser);
            $this->entityManager->flush();
        }

        // Créer un rôle de test
        $role = new Roles();
        $role->setName('Étudiant'); // Exemple de rôle
        $this->entityManager->persist($role);
        $this->entityManager->flush();

        // Créer un utilisateur de test
        $user = new User();
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setEmail('test@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setHasRole($role); // Assigner un rôle à l'utilisateur
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function testLoginSuccess(): void
    {
        $this->client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('redirect', $data);
    }

    public function testLoginFailure(): void
    {
        $this->client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]));

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // éviter les fuites de mémoire
    }
}