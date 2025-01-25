<?php
namespace App\Controller;

use App\Entity\CallAlerts;
use App\Entity\Cohortes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPERVISOR')]
class SupervisorController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/cohortes', name: 'get_cohortes', methods: ['GET'])]
    public function viewCohorts(): JsonResponse
    {
        $cohorts = $this->entityManager->getRepository(Cohortes::class)->findAll();

        return new JsonResponse(['cohortes' => $cohorts], JsonResponse::HTTP_OK);
    }

    #[Route('/api/call_alerts', name: 'get_call_alerts', methods: ['GET'])]
    public function viewCalls(): JsonResponse
    {
        $calls = $this->entityManager->getRepository(CallAlerts::class)->findBy(['handled' => false]);

        return new JsonResponse(['CallAlerts' => $calls], JsonResponse::HTTP_OK);
    }

    #[Route('/api/call_alerts/{id}', name: 'put_call_alert', methods: ['PUT'])]
    public function markCallAsHandled(int $id): JsonResponse
    {
        $call = $this->entityManager->getRepository(CallAlerts::class)->find($id);
        if (!$call) {
            return new JsonResponse(['message' => 'Appel non trouvé.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $call->setCallStatut(true);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Appel marqué comme géré.'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/cohortes', name: 'add_cohort', methods: ['POST'])]
    public function addTemporaryCohort(Request $request): JsonResponse
    {
        // Récupérer les données de la requête
        $data = json_decode($request->getContent(), true);
    
        // Vérifier que le JSON est valide
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['message' => 'Données JSON invalides.'], 400);
        }
    
        // Vérifier que les données nécessaires sont présentes
        if (!isset($data['name']) || !isset($data['start_date']) || !isset($data['end_date'])) {
            return new JsonResponse(['message' => 'Les champs name, start_date et end_date sont requis.'], 400);
        }
    
        // Logique pour ajouter une cohorte temporaire
        $cohort = new Cohortes();
        $cohort->setName($data['name']);
        $cohort->setStartDate(new \DateTime($data['start_date']));
        $cohort->setEndDate(new \DateTime($data['end_date']));
        $cohort->setTemporary(true);
    
        $this->entityManager->persist($cohort);
        $this->entityManager->flush();
    
        return new JsonResponse(['id' => $cohort->getId(), 'message' => 'Cohorte temporaire ajoutée avec succès.'], 201);
    }
}