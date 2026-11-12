<?php

use App\Tools\NavigationTools;
use App\Entity\User;

$mainMenu = require_once __DIR__ . '/../App/Config/menu.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite&Gourmand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/public/assets/css/override-bootstrap.css">
</head>

<body data-theme="gourmand">
    <div class="container">
        <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-between py-3 mb-4 border-bottom border-dark border-2">
            <div class="col-12 col-lg-auto mb-2 mb-lg-0 text-center">
                <a href="/" class="d-inline-flex link-body-emphasis text-decoration-none">
                    <img src="/public/assets/images/logo_fond_or.svg" class="bi" width="80" alt="Logo Vite&Gourmand">
                </a>
            </div>
            <ul class="nav col-12 col-lg-auto mb-2 mb-lg-0 justify-content-center">
                <?php foreach ($mainMenu as $item) {  ?>
                    <li><a href="/index.php?controller=<?= $item['controller'] ?>&action=<?= $item['action'] ?>" class="nav-link px-2 <?= NavigationTools::addActiveClass($item['controller'], $item['action']) ?>"><?= $item['label'] ?></a></li>
                <?php } ?>
            </ul>
            <div class="col-12 col-lg-auto d-flex flex-wrap gap-2 justify-content-center align-items-center">
                <?php if (User::isLogged()) { ?>
                    <span>Bonjour <?= htmlspecialchars(ucfirst($_SESSION['user']['prenom'])) ?></span>
                    <a href="/index.php?controller=compte&action=commandes" class="btn btn-outline-primary">Mon espace</a>
                    <a href="/index.php?controller=auth&action=logout" class="btn btn-outline-primary">Déconnexion</a>
                <?php } else { ?>
                    <a href="/index.php?controller=auth&action=login" class="btn btn-outline-primary">Connexion</a>
                    <a href="/index.php?controller=user&action=register" class="btn btn-primary">Inscription</a>
                <?php } ?>
            </div>
        </header>
    </div>
    <main>