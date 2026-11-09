<?php

namespace App\Controller;

use App\Repository\UserRepository;

class AuthController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'login':
                        $this->login();
                        break;
                    case 'logout':
                        $this->logout();
                        break;
                    default:
                        throw new \Exception("Cette action n'existe pas : " . $_GET['action']);
                        break;
                }
            } else {
                throw new \Exception("Aucune action détectée");
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }


    protected function login()
    {
        $errors = [];

        if (isset($_POST['loginUser'])) {

            $userRepository = new UserRepository();
            $user = $userRepository->findOneByEmail($_POST['email']);

            if ($user && $user->verifyPassword($_POST['password'])) {

                if (!$user->isActif()) {
                    $errors[] = 'Ce compte a été désactivé. Contactez l\'administrateur.';
                } else {
                    // Mémorisation de l'email si la case est cochée
                    if (!empty($_POST['remember_email'])) {
                        setcookie('remember_email', $user->getEmail(), [
                            'expires' => time() + 30 * 24 * 3600,
                            'path' => '/',
                            'httponly' => true,
                            'samesite' => 'Lax',
                        ]);
                    } else {
                        setcookie('remember_email', '', [
                            'expires' => time() - 3600,
                            'path' => '/',
                        ]);
                    }
                    // Regénère l'id de session pour éviter la fixation de session
                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'id' => $user->getUtilisateurId(),
                        'email' => $user->getEmail(),
                        'prenom' => $user->getPrenom(),
                        'nom' => $user->getNom(),
                        'role_id' => $user->getRoleId(),
                    ];
                    // On renvoie vers la page mémorisée s'il y en a une, sinon vers l'accueil
                    $redirection = $_SESSION['redirect_after_login'] ?? 'index.php';
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirection);
                    exit;
                }
            } else {
                $errors[] = 'Email ou mot de passe incorrect';
            }
        }

        // Message flash et email venant de l'inscription, lus une seule fois
        $emailPrerempli = $_SESSION['inscription_email'] ?? ($_COOKIE['remember_email'] ?? '');
        $succes = $_SESSION['message_succes'] ?? null;
        unset($_SESSION['inscription_email'], $_SESSION['message_succes']);

        $this->render('auth/login', [
            'errors' => $errors,
            'emailPrerempli' => $emailPrerempli,
            'succes' => $succes,
        ]);
    }


    protected function logout()
    {
        //Prévient les attaques de fixation de session
        session_regenerate_id(true);
        //Supprime les données de session du serveur
        session_destroy();
        //Supprime les données du tableau $_SESSION
        unset($_SESSION);
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}
