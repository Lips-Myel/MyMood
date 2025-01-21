<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        // Chemin vers le fichier index.html
        $filePath = $this->getParameter('kernel.project_dir') . '/public/index.html';

        // Vérifiez si le fichier existe
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier index.html n\'existe pas.');
        }

        // Retourne le contenu du fichier HTML
        return new Response(file_get_contents($filePath), 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
