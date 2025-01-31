<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

class MailerService extends AbstractController
{
    private MailerInterface $mailer;
    private Environment $twig;
    
    public function __construct(MailerInterface $mailer, Environment $twig)
        {
            $this->mailer = $mailer;
            $this->twig = $twig;
        }
    
        public function sendAccountCreationEmail(string $email, string $password)
        {
            $htmlContent = $this->twig->render('emails/account_creation.html.twig', [
                'email' => $email,
                'password' => $password,
            ]);    
    
        $email = (new Email())
            ->from('ludovic.picaud@institutsolacroup.com')
            ->replyTo('ludovic.picaud@institutsolacroup.com')
            ->to($email)
            ->subject('Votre compte à eté créé sur MyMoodApp')
            
            ->html($htmlContent);

        $this->mailer->send($email);

        return $this->json(['message' => 'E-mail envoyé avec succès !']);
    }
}
