<?php

namespace App\Controller;

use App\Entity\Roles;
use App\Entity\User;
use App\Service\MailerService;
use App\Service\PasswordFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Routing\Annotation\Route;

class UserCreateController extends AbstractController
{
    private PasswordFactory $passwordFactory;
    private EntityManagerInterface $entityManager;
    private MailerService $mailerService;

    public function __construct(
        PasswordFactory $passwordFactory,
        EntityManagerInterface $entityManager,
        MailerService $mailerService
    ) {
        $this->passwordFactory = $passwordFactory;
        $this->entityManager = $entityManager;
        $this->mailerService = $mailerService;
    }

    #[Route('/create-users', name: 'app_create_users', methods: ['POST'])]
    public function __invoke(Request $request, ValidatorInterface $validator): JsonResponse
    {
        // Récupérer les données JSON envoyées
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        // Définition des contraintes de validation
        $constraints = new Assert\Collection([
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'firstName' => [new Assert\NotBlank()],
            'lastName' => [new Assert\NotBlank()],
            'hasRole' => [new Assert\Optional()],  // Rôles valides
        ]);

        // Valider les données
        $errors = $validator->validate($data, $constraints);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        // Vérifier si l'email existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Cet email est déjà utilisé.'], 400);
        }

        // Créer un nouvel utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setBlackList(false); // Par défaut, l'utilisateur n'est pas dans la liste noire

        // Récupérer ou définir un rôle par défaut (1 = Étudiant)
        $roleId = $data['hasRole'] ?? 4; // Si 'hasRole' n'est pas présent, assigner le rôle par défaut 1 (Étudiant)

        // Validation du rôle dans la base de données
        $role = $this->entityManager->getRepository(Roles::class)->find($roleId);
        if (!$role) {
            return new JsonResponse(['error' => 'Rôle non trouvé.'], 400);
        }

        // Assigner le rôle à l'utilisateur
        $user->setHasRole($role);

        // Générer et hacher un mot de passe aléatoire
        $password = $this->passwordFactory->createHashedPassword();
        $plainPassword = $password['plain'];   
        $hashedPassword = $password['hashed']; 
        $user->setPassword($hashedPassword); // Assigner le mot de passe haché à l'utilisateur

        // Envoi de l'email et persistance des données
        try {
            $this->mailerService->sendAccountCreationEmail($user->getEmail(), $plainPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Utilisateur créé avec succès, email envoyé.']);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()], 500);
        }
    }
}
