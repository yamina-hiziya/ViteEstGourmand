<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\StatistiqueRepository;
use App\Repository\MenuRepository;
use App\Repository\CommandeRepository;
use App\Tools\Mailer;

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
                    case 'statistiques':
                        $this->statistiques();
                        break;
                    case 'synchroniser':
                        $this->synchroniser();
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
                // L'ECF impose de ne pas communiquer le mot de passe dans le mail
                Mailer::envoyer(
                    $nouveau->getEmail(),
                    'Votre compte employé Vite & Gourmand',
                    '<p>Bonjour ' . htmlspecialchars(ucfirst($nouveau->getPrenom())) . ',</p>
                    <p>Un compte employé a été créé pour vous sur le site Vite &amp; Gourmand, avec cette adresse email comme identifiant.</p>
                    <p>Pour des raisons de sécurité, votre mot de passe ne vous est pas communiqué par mail : merci de vous rapprocher de l\'administrateur pour l\'obtenir.</p>'
                );
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

    protected function statistiques()
    {
        $menuId = (int) ($_GET['menu_id'] ?? 0) ?: null;
        $debut = $_GET['debut'] ?? '';
        $fin = $_GET['fin'] ?? '';

        // On n'accepte que des dates au format AAAA-MM-JJ
        $debut = preg_match('/^\d{4}-\d{2}-\d{2}$/', $debut) ? $debut : null;
        $fin = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fin) ? $fin : null;

        $statistiques = [];
        $erreur = null;
        try {
            $statistiques = (new StatistiqueRepository())->statistiquesParMenu($menuId, $debut, $fin);
        } catch (\Exception $e) {
            $erreur = 'Les statistiques sont momentanément indisponibles.';
        }

        $succes = $_SESSION['message_succes'] ?? null;
        unset($_SESSION['message_succes']);

        $this->render('admin/statistiques', [
            'statistiques' => $statistiques,
            'totalCommandes' => array_sum(array_column($statistiques, 'nb_commandes')),
            'totalChiffreAffaires' => array_sum(array_column($statistiques, 'chiffre_affaires')),
            'menus' => (new MenuRepository())->findAll(),
            'menuId' => $menuId,
            'debut' => $debut,
            'fin' => $fin,
            'erreur' => $erreur,
            'succes' => $succes,
        ]);
    }

    protected function synchroniser()
    {
        if (!isset($_POST['synchroniser'])) {
            throw new \Exception('Action non autorisée.');
        }

        $statistiqueRepository = new StatistiqueRepository();
        $commandes = (new CommandeRepository())->findAllPourStatistiques();

        foreach ($commandes as $commande) {
            $statistiqueRepository->enregistrerCommande(
                $commande['numero_commande'],
                (int) $commande['menu_id'],
                $commande['menu_titre'],
                (float) $commande['chiffre_affaires'],
                $commande['statut'],
                $commande['date_commande']
            );
        }

        $_SESSION['message_succes'] = count($commandes) . ' commandes synchronisées avec MongoDB.';
        header('Location: index.php?controller=admin&action=statistiques');
        exit;
    }
}
