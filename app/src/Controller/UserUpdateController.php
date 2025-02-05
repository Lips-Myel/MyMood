<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PasswordFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PasswordFactory $passwordFactory;

    public function __construct(EntityManagerInterface $entityManager, PasswordFactory $passwordFactory)
    {
        $this->entityManager = $entityManager;
        $this->passwordFactory = $passwordFactory;
    }

    // Mettre à jour un utilisateur (y compris le mot de passe)
    #[Route('/users/{id}', name: 'app_update_user', methods: ['PATCH'])]
    public function updateUser(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }

        // Définition des contraintes de validation
        $constraints = new Assert\Collection([
            'firstName' => [new Assert\Optional([new Assert\NotBlank()])],
            'lastName' => [new Assert\Optional([new Assert\NotBlank()])],
            'email' => [new Assert\Optional([new Assert\NotBlank(), new Assert\Email()])],
            'isBlackListed' => [new Assert\Optional([new Assert\Type('bool')])],
            'lastConnection' => [new Assert\Optional([new Assert\DateTime()])],
            'password' => [new Assert\Optional([new Assert\NotBlank(), new Assert\Length(['min' => 8])])],
        ]);

        $errors = $validator->validate($data, $constraints);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        // Mise à jour des données si elles sont présentes
        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }
        if (isset($data['email'])) {
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser && $existingUser->getId() !== $id) {
                return new JsonResponse(['error' => 'Cet email est déjà utilisé.'], 400);
            }
            $user->setEmail($data['email']);
        }
        if (isset($data['isBlackListed'])) {
            $user->setBlackList($data['isBlackListed']);
        }
        if (isset($data['lastConnection'])) {
            $user->setLastConnection(new \DateTime($data['lastConnection']));
        }
        if (isset($data['password'])) {
            $hashedPassword = $this->passwordFactory->hashPassword($data['password']);
            $user->setPassword($hashedPassword);
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur mis à jour avec succès']);
    }

    // Réinitialiser le mot de passe
    #[Route('/users/{id}/reset-password', name: 'app_reset_password', methods: ['POST'])]
    public function resetPassword(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        // Générer un nouveau mot de passe
        $passwordData = $this->passwordFactory->createHashedPassword();
        $plainPassword = $passwordData['plain'];
        $hashedPassword = $passwordData['hashed'];

        // Mettre à jour le mot de passe de l'utilisateur
        $user->setPassword($hashedPassword);
        $this->entityManager->flush();

        // Envoi du mot de passe par email (ajouter un service d'email si nécessaire)
        // $this->mailerService->sendResetPasswordEmail($user->getEmail(), $plainPassword);

        return new JsonResponse(['message' => 'Mot de passe réinitialisé avec succès', 'newPassword' => $plainPassword]);
    }
}
