<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var array $commande */
/** @var array $lignes */
/** @var array $suivi */
$total = $commande['prix_menu'] + $commande['prix_livraison'];
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <a href="index.php?controller=compte&action=commandes" class="d-inline-block mb-3">← Mes commandes</a>
            <h1 class="mb-1">Commande <?= htmlspecialchars($commande['numero_commande']) ?></h1>
            <p class="text-muted mb-4"><?= htmlspecialchars($commande['menu_titre']) ?></p>

            <?php if (!empty($succes)) { ?>
                <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
            <?php } ?>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5">La prestation</h2>
                    <ul class="list-unstyled mb-0">
                        <li><strong>Date :</strong> <?= date('d/m/Y', strtotime($commande['date_prestation'])) ?> à <?= htmlspecialchars(substr($commande['heure_livraison'], 0, 5)) ?></li>
                        <li><strong>Adresse :</strong> <?= htmlspecialchars($commande['adresse_livraison']) ?>, <?= htmlspecialchars($commande['ville_livraison']) ?></li>
                        <li><strong>Nombre de personnes :</strong> <?= $commande['nombre_personne'] ?></li>
                        <li><strong>Total :</strong> <?= number_format($total, 2, ',', ' ') ?> €</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5">Les plats</h2>
                    <ul class="mb-0">
                        <?php foreach ($lignes as $ligne) { ?>
                            <li><?= htmlspecialchars(ucfirst($ligne['type_plat'])) ?> : <?= htmlspecialchars($ligne['titre_plat']) ?> × <?= $ligne['quantite'] ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5">Suivi de la commande</h2>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($suivi as $etape) { ?>
                            <li class="list-group-item px-0">
                                <strong><?= htmlspecialchars(ucfirst($etape['statut'])) ?></strong>
                                <span class="text-muted"> — le <?= date('d/m/Y à H:i', strtotime($etape['date_heure'])) ?></span>
                                <?php if ($etape['motif']) { ?>
                                    <div class="small text-muted"><?= htmlspecialchars($etape['motif']) ?></div>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <?php if ($commande['statut'] === 'en attente') { ?>
                <form method="POST" action="index.php?controller=compte&action=annuler" onsubmit="return confirm('Voulez-vous vraiment annuler cette commande ?');">
                    <input type="hidden" name="numero_commande" value="<?= htmlspecialchars($commande['numero_commande']) ?>">
                    <button type="submit" name="annulerCommande" class="btn btn-outline-danger">Annuler ma commande</button>
                </form>
            <?php } ?>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>