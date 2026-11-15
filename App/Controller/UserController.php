<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Tools\Mailer;


class UserController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'register':
                        $this->register();
                        break;
                    case 'delete':
                        // Appeler méthode delete()
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

    protected function register()
    {
        try {
            $errors = [];
            $user = new User();

            if (isset($_POST['saveUser'])) {

                // 1. On ne garde que les champs attendus (protection contre l'assignation de masse)
                $champsAutorises = ['nom', 'prenom', 'email', 'telephone', 'adresse_postale', 'ville', 'pays', 'password'];
                $donnees = array_intersect_key($_POST, array_flip($champsAutorises));
                $donnees = array_map('trim', $donnees);

                $user->hydrate($donnees);

                // 2. Le rôle est imposé par le serveur : 1 = utilisateur
                $user->setRoleId(1);

                // 3. Validation des champs
                $errors = $user->validate();

                // 4. Vérifications propres à l'inscription
                if (empty($_POST['consentement'])) {
                    $errors['consentement'] = 'Vous devez accepter l\'utilisation de vos données pour créer un compte';
                }
                if (($_POST['password'] ?? '') !== ($_POST['password_confirm'] ?? '')) {
                    $errors['password_confirm'] = 'Les deux mots de passe ne sont pas identiques';
                }

                $userRepository = new UserRepository();
                if (!isset($errors['email']) && $userRepository->findOneByEmail($user->getEmail())) {
                    $errors['email'] = 'Un compte existe déjà avec cet email';
                }

                // 5. Aucune erreur : on enregistre et on redirige vers la connexion
                if (empty($errors)) {
                    $userRepository->persist($user);
                    // Mail de bienvenue (si l'envoi échoue, l'inscription reste valide)
                    Mailer::envoyer(
                        $user->getEmail(),
                        'Bienvenue chez Vite & Gourmand',
                        '<p>Bonjour ' . htmlspecialchars(ucfirst($user->getPrenom())) . ',</p>
                            <p>Votre compte a bien été créé. Vous pouvez dès maintenant découvrir nos menus et passer commande pour vos événements.</p>
                            <p>À très bientôt,<br>Julie et José</p>'
                    );
                    $_SESSION['inscription_email'] = $user->getEmail();
                    $_SESSION['message_succes'] = 'Votre compte a bien été créé. Vous pouvez maintenant vous connecter.';
                    header('Location: index.php?controller=auth&action=login');
                    exit;
                }
            }

            $this->render('user/add_edit', [
                'pageTitle' => 'Inscription',
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
