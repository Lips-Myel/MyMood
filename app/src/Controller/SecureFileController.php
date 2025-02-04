<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecureFileController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/{folder}/{filename}', name: 'secure_files', requirements: ['folder' => 'admin|supervisor|student', 'filename' => '.+'])]
    public function serveFile(string $folder, string $filename, Request $request): Response
    {
        // 📝 Construction du chemin sécurisé
        $folderPath = realpath($this->getParameter('kernel.project_dir') . "/secured/{$folder}");
        $filePath = realpath("{$folderPath}/{$filename}");

        // 🔎 Vérifier que le fichier existe et qu'il est bien dans le bon dossier
        if (!$filePath || !str_starts_with($filePath, $folderPath) || !is_file($filePath)) {
            $this->logger->error("Fichier introuvable ou accès non autorisé : $filePath");
            throw new NotFoundHttpException('Fichier introuvable');
        }

        // 📄 Retourner le fichier avec son bon type MIME
        return new BinaryFileResponse($filePath);
    }
}
