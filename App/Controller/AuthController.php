<?php

namespace App\Controller;
use App\Entity\User;
use App\Tools\Mailer;

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
                    case 'oubli':
                        $this->oubli();
                        break;
                    case 'reinitialiser':
                        $this->reinitialiser();
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

    protected function oubli()
    {
        $envoye = false;

        if (isset($_POST['demanderLien'])) {
            $email = trim($_POST['email'] ?? '');
            $userRepository = new UserRepository();
            $user = filter_var($email, FILTER_VALIDATE_EMAIL) ? $userRepository->findOneByEmail($email) : false;

            if ($user && $user->isActif()) {
                // Jeton aléatoire : seule son empreinte est enregistrée en base
                $jeton = bin2hex(random_bytes(32));
                $userRepository->enregistrerJeton($user->getUtilisateurId(), hash('sha256', $jeton));

                $lien = Mailer::url('index.php?controller=auth&action=reinitialiser&token=' . $jeton);
                Mailer::envoyer(
                    $user->getEmail(),
                    'Réinitialisation de votre mot de passe',
                    '<p>Bonjour ' . htmlspecialchars(ucfirst($user->getPrenom())) . ',</p>
                 <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous pour en choisir un nouveau :</p>
                 <p><a href="' . htmlspecialchars($lien) . '" style="background: #1B4A8A; color: #ffffff; padding: 10px 16px; border-radius: 6px; text-decoration: none;">Choisir un nouveau mot de passe</a></p>
                 <p>Ce lien est valable pendant 1 heure. Si vous n\'êtes pas à l\'origine de cette demande, ignorez simplement ce message.</p>'
                );
            }

            // Toujours le même message, que le compte existe ou non
            $envoye = true;
        }

        $this->render('auth/oubli', [
            'envoye' => $envoye,
        ]);
    }

    protected function reinitialiser()
    {
        $jeton = $_GET['token'] ?? '';
        $userRepository = new UserRepository();
        $user = $jeton !== '' ? $userRepository->findOneByJetonValide(hash('sha256', $jeton)) : false;
        $errors = [];

        if (!$user) {
            $this->render('auth/reinitialiser', [
                'valide' => false,
                'errors' => [],
            ]);
            return;
        }

        if (isset($_POST['saveMotDePasse'])) {
            $motDePasse = $_POST['password'] ?? '';
            $confirmation = $_POST['password_confirm'] ?? '';

            $erreur = User::verifierFormatMotDePasse($motDePasse);
            if ($erreur !== null) {
                $errors['password'] = $erreur;
            } elseif ($motDePasse !== $confirmation) {
                $errors['password_confirm'] = 'Les deux mots de passe ne sont pas identiques';
            }

            if (empty($errors)) {
                $userRepository->changerMotDePasse($user->getUtilisateurId(), $motDePasse);
                $_SESSION['message_succes'] = 'Votre mot de passe a bien été modifié. Vous pouvez vous connecter.';
                $_SESSION['inscription_email'] = $user->getEmail();
                header('Location: index.php?controller=auth&action=login');
                exit;
            }
        }

        $this->render('auth/reinitialiser', [
            'valide' => true,
            'errors' => $errors,
        ]);
    }
}
