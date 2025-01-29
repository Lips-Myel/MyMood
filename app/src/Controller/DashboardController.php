<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/student/dashboard', name: 'student_dashboard', methods:['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function studentDashboardGet()
    {
        // Logique pour le tableau de bord de l'étudiant
        return new JsonResponse(data: [
            'message' => 'Tableau de bord de l\'étudiant',
        ]);
    }
    #[Route('/student/dashboard', name: 'student_dashboard', methods:['POST'])]
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