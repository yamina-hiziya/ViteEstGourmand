<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

class AdminController extends Controller
{
    public function route(): void
    {
        try {
            if (!User::isAdmin()) {
                throw new \Exception('Accès réservé à l\'administrateur.');
            }

            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'employes':
                        $this->employes();
                        break;
                    case 'activer':
                        $this->activer();
                        break;
                    default:
                        throw new \Exception("Cette action n'existe pas : " . $_GET['action']);
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

    protected function employes()
    {
        $userRepository = new UserRepository();
        $errors = [];
        $nouveau = new User();

        if (isset($_POST['creerEmploye'])) {
            $nouveau->setNom(trim($_POST['nom'] ?? ''));
            $nouveau->setPrenom(trim($_POST['prenom'] ?? ''));
            $nouveau->setEmail(trim($_POST['email'] ?? ''));
            $nouveau->setPassword($_POST['password'] ?? '');

            // Valeurs par défaut pour les champs qui ne concernent pas un employé
            $nouveau->setTelephone('Non renseigné');
            $nouveau->setAdressePostale('12 rue des Gourmets');
            $nouveau->setVille('Bordeaux');
            $nouveau->setPays('France');

            // Le rôle est imposé par le serveur : impossible de créer un administrateur
            $nouveau->setRoleId(2);

            $errors = $nouveau->validate();

            if (!isset($errors['email']) && $userRepository->findOneByEmail($nouveau->getEmail())) {
                $errors['email'] = 'Un compte existe déjà avec cet email.';
            }

            if (empty($errors)) {
                $userRepository->persist($nouveau);
                $_SESSION['message_succes'] = 'Le compte employé a bien été créé.';
                header('Location: index.php?controller=admin&action=employes');
                exit;
            }
        }

        $succes = $_SESSION['message_succes'] ?? null;
        unset($_SESSION['message_succes']);

        $this->render('admin/employes', [
            'employes' => $userRepository->findAllEmployes(),
            'nouveau' => $nouveau,
            'errors' => $errors,
            'succes' => $succes,
        ]);
    }

    protected function activer()
    {
        $id = (int) ($_POST['utilisateur_id'] ?? 0);
        $actif = ($_POST['actif'] ?? '') === '1';

        $userRepository = new UserRepository();
        if ($userRepository->changerActif($id, $actif)) {
            $_SESSION['message_succes'] = $actif ? 'Le compte a été réactivé.' : 'Le compte a été désactivé.';
        }

        header('Location: index.php?controller=admin&action=employes');
        exit;
    }
}
