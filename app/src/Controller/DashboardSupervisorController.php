<?php
namespace App\Controller;

use App\Entity\Cohortes;
use App\Repository\CallAlertsRepository;
use App\Repository\CohorteRepository;
use App\Repository\CallAlertRepository;
use App\Repository\CohortesRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardSupervisorController extends AbstractController
{
    private $cohorteRepository;
    private $callAlertRepository;
    private $notificationService;
    private $entityManager;
    private $userRepository;

    public function __construct(
        CohortesRepository $cohorteRepository,
        CallAlertsRepository $callAlertRepository,
        //NotificationService $notificationService,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->cohorteRepository = $cohorteRepository;
        $this->callAlertRepository = $callAlertRepository;
        //$this->notificationService = $notificationService;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    #[Route('/api/supervisor/cohortes', name: 'supervisor_cohortes', methods: ['GET'])]
    #[IsGranted('ROLE_SUPERVISOR')]
    public function viewCohortes(): JsonResponse
    {
        $supervisor = $this->getUser();
        $cohortes = $this->cohorteRepository->findBySupervisor($supervisor);

        $data = array_map(function ($cohorte) {
            return [
                'id' => $cohorte->getId(),
                'name' => $cohorte->getName(),
                'start_date' => $cohorte->getStartDate()->format('Y-m-d H:i:s'),
                'end_date' => $cohorte->getEndDate()->format('Y-m-d H:i:s'),
                'temporary' => $cohorte->isTemporary(),
                'moods' => array_map(function ($student) {
                    return [
                        'id' => $student->getId(),
                        'name' => $student->getName(),
                        'mood' => $student->getMood(),
                    ];
                }, $cohorte->getStudents()->toArray())
            ];
        }, $cohortes);

        return new JsonResponse($data);
    }

    #[Route('/api/supervisor/call-alerts', name: 'supervisor_call_alerts', methods: ['GET'])]
    #[IsGranted('ROLE_SUPERVISOR')]
    public function viewCallAlerts(): JsonResponse
    {
        $alerts = $this->callAlertRepository->findRecentAlerts();

        $data = array_map(function ($alert) {
            return [
                'id' => $alert->getId(),
                'student' => [
                    'id' => $alert->getStudent()->getId(),
                    'name' => $alert->getStudent()->getName()
                ],
                'created_at' => $alert->getCreatedAt()->format('Y-m-d H:i:s'),
                'resolved' => $alert->isResolved()
            ];
        }, $alerts);

        return new JsonResponse($data);
    }

    #[Route('/api/supervisor/resolve-call/{id}', name: 'resolve_call_alert', methods: ['PATCH'])]
    #[IsGranted('ROLE_SUPERVISOR')]
    public function resolveCallAlert(int $id): JsonResponse
    {
        $alert = $this->callAlertRepository->find($id);

        if (!$alert) {
            return new JsonResponse(['erreur' => 'Alerte non trouvée'], 404);
        }

        $alert->setResolved(true);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Alerte marquée comme résolue']);
    }

   /* #[Route('/api/supervisor/notifications', name: 'send_notifications', methods: ['POST'])]
    #[IsGranted('ROLE_SUPERVISOR')]
    public function sendNotifications(): JsonResponse
    {
        $alerts = $this->callAlertRepository->findUnresolvedAlerts();

        foreach ($alerts as $alert) {
            $this->notificationService->notifySupervisor($alert);
        }

        return new JsonResponse(['message' => 'Notifications envoyées']);
    }*/

    #[Route('/api/supervisor/cohorte/create', name: 'create_cohorte', methods: ['POST'])]
    #[IsGranted('ROLE_SUPERVISOR')]
    public function createCohorte(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifier si les informations nécessaires sont présentes
        if (!isset($data['name'], $data['start_date'], $data['end_date'], $data['student_ids'])) {
            return new JsonResponse(['erreur' => 'Données manquantes'], 400);
        }

        // Récupérer les étudiants existants via leurs IDs
        $students = $this->userRepository->findBy(['id' => $data['student_ids']]);

        if (count($students) !== count($data['student_ids'])) {
            return new JsonResponse(['erreur' => 'Certains étudiants n\'existent pas'], 404);
        }

        // Créer la cohorte temporaire
        $cohorte = new Cohortes();
        $cohorte->setName($data['name']);
        $cohorte->setStartDate(new \DateTime($data['start_date']));
        $cohorte->setEndDate(new \DateTime($data['end_date']));
        $cohorte->setTemporary(true);
        //$cohorte->setSupervisor($this->getUser());

        // Associer les étudiants à la cohorte
        /*foreach ($students as $student) {
            $cohorte->addStudent($student); // Assurez-vous que vous avez une méthode addStudent dans votre entité Cohorte
        }*/

        // Enregistrer la cohorte dans la base de données
        $this->entityManager->persist($cohorte);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Cohorte temporaire créée avec succès'], 201);
    }
}
