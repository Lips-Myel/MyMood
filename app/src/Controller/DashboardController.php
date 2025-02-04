<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/users', name: 'student_dashboard', methods:['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function studentDashboardGet(UserRepository $userRepository): JsonResponse
    {
        $students = $userRepository->findAll(); // À remplacer par findByRole('ROLE_STUDENT') si possible

        return $this->json(array_map(function ($student) {
            return [
                "nom" => $student->getLastName(),
                "prenom" => $student->getFirstName(),
                "email" => $student->getEmail(),
                "password" => $student->getPassword(),
                "role" => $student->getRoles()[0] ?? 'ROLE_USER', // Vérification si le tableau de rôles existe
                /*
                          "first_name": "string",
      "last_name": "string",
      "email": "string",
      "password": "string",
      "last_connection": "2025-02-03T09:10:14.593Z",
      "black_list": true,
      "has_mood": "https://example.com/",
      "callAlerts": "https://example.com/",
      "has_role": "https://example.com/",
      "cohortes": [
        "https://example.com/"
      ],
                */
            ];
        }, $students));
    }

    #[Route('/api/users', name: 'student_dashboard', methods:['POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function studentDashboardPost()
    {
        
        // Logique pour le tableau de bord de l'étudiant
        return new JsonResponse([
            // score
            'message' => 'Tableau de bord de l\'étudiant',
        ]);
    }

    #[Route('/supervisor/dashboard', name: 'supervisor_dashboard')]
    #[IsGranted('ROLE_SUPERVISOR')]
    public function supervisorDashboard()
    {
        // Logique pour le tableau de bord du superviseur
        return new JsonResponse([
            'message' => 'Tableau de bord du superviseur',
        ]);
    }
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDashboard()
    {
        // Logique pour le tableau de bord de l'administrateur
        return new JsonResponse([
            'message' => 'Tableau de bord de l\'administrateur',
        ]);
    }
}