<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var string $numero */
?>

<div class="container my-5 text-center">
    <h1 class="mb-3">Merci pour votre commande !</h1>
    <p class="fs-5">Votre commande <strong><?= htmlspecialchars($numero) ?></strong> a bien été enregistrée.</p>
    <p>Elle est en attente de validation par notre équipe. Vous pourrez suivre son avancement depuis votre espace.</p>
    <a href="index.php?controller=menu&action=list" class="btn btn-primary mt-3">Retour aux menus</a>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>