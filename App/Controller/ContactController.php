<?php

namespace App\Controller;

use App\Tools\Mailer;

class ContactController extends Controller
{
    public function route(): void
    {
        try {
            $action = $_GET['action'] ?? 'index';
            switch ($action) {
                case 'index':
                    $this->index();
                    break;
                default:
                    throw new \Exception("Cette action n'existe pas : " . $action);
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function index()
    {
        $errors = [];

        if (isset($_POST['envoyerContact'])) {
            // Champ piège : invisible pour un humain, rempli par les robots
            if (!empty($_POST['site_web'])) {
                header('Location: index.php?controller=contact&action=index');
                exit;
            }

            $titre = trim($_POST['titre'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if ($titre === '' || mb_strlen($titre) > 150) {
                $errors['titre'] = 'Indiquez un titre de 150 caractères maximum.';
            }
            if ($message === '') {
                $errors['message'] = 'Le message ne peut pas être vide.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Indiquez une adresse email valide, pour que nous puissions vous répondre.';
            }

            if (empty($errors)) {
                $config = require _ROOTPATH_ . '/App/Config/mail_config.php';

                $envoye = Mailer::envoyer(
                    $config['contact_email'],
                    'Nouvelle demande de contact : ' . $titre,
                    '<p><strong>De :</strong> ' . htmlspecialchars($email) . '</p>
                     <p><strong>Titre :</strong> ' . htmlspecialchars($titre) . '</p>
                     <p><strong>Message :</strong></p>
                     <p>' . nl2br(htmlspecialchars($message)) . '</p>',
                    $email
                );

                if ($envoye) {
                    $_SESSION['message_succes'] = 'Merci ! Votre message a bien été envoyé. Nous vous répondrons rapidement.';
                    header('Location: index.php?controller=contact&action=index');
                    exit;
                }
                $errors['general'] = 'Votre message n\'a pas pu être envoyé. Merci de réessayer plus tard.';
            }
        }

        $succes = $_SESSION['message_succes'] ?? null;
        unset($_SESSION['message_succes']);

        $this->render('contact/index', [
            'errors' => $errors,
            'succes' => $succes,
        ]);
    }
}