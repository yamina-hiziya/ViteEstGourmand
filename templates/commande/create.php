<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Menu $menu */
/** @var App\Entity\Plat[] $plats */
?>

<div class="container my-5">
    <h1 class="mb-4">Commander : <?= htmlspecialchars($menu->getTitre()) ?></h1>
    <p>Le formulaire de commande arrive à l'étape suivante.</p>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>