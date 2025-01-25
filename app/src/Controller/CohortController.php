<?php
namespace App\Controller;

use App\Repository\CohortesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CohortController extends AbstractController
{
    #[Route('api/cohortes', name: 'get_cohorts', methods: ['GET'])]
    public function getCohorts(CohortesRepository $cohortRepository): JsonResponse
    {
        $supervisor = $this->getUser(); // Assume que le superviseur est l'utilisateur connecté
        $cohorts = $cohortRepository->findBy(['supervisor' => $supervisor]);

        $data = [];
        foreach ($cohorts as $cohort) {
            $students = array_map(fn($student) => [
                'id' => $student->getId(),
                'name' => $student->getName(),
                'mood' => $student->getMood(),
            ], $cohort->getStudents()->toArray());

            $data[] = [
                'id' => $cohort->getId(),
                'name' => $cohort->getName(),
                'temporary' => $cohort->isTemporary(),
                'students' => $students,
            ];
        }

        return $this->json($data);
    }
}
