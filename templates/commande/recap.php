<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Menu $menu */
/** @var array $commande */
$reduction = $commande['prix_sans_reduction'] - $commande['prix_menu'];
$total = $commande['prix_menu'] + $commande['prix_livraison'];
$types = ['entrée' => 'Entrées', 'plat' => 'Plats', 'dessert' => 'Desserts'];
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-4">Récapitulatif de votre commande</h1>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5"><?= htmlspecialchars($menu->getTitre()) ?></h2>
                    <ul class="list-unstyled mb-0">
                        <li><strong>Date :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($commande['date_prestation']))) ?> à <?= htmlspecialchars($commande['heure_livraison']) ?></li>
                        <li><strong>Adresse :</strong> <?= htmlspecialchars($commande['adresse_livraison']) ?>, <?= htmlspecialchars($commande['ville_livraison']) ?></li>
                        <li><strong>Nombre de personnes :</strong> <?= $commande['nombre_personne'] ?></li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5">Vos plats</h2>
                    <?php foreach ($types as $type => $titreType) { ?>
                        <h3 class="h6 mt-3"><?= $titreType ?></h3>
                        <ul class="mb-0">
                            <?php foreach ($commande['lignes'] as $ligne) {
                                if ($ligne['type'] !== $type) {
                                    continue;
                                }
                            ?>
                                <li><?= htmlspecialchars($ligne['titre']) ?> × <?= $ligne['quantite'] ?></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5">Détail du prix</h2>
                    <table class="table mb-0">
                        <tr>
                            <td>Menu (<?= $commande['nombre_personne'] ?> × <?= number_format($menu->getPrixParPersonne(), 2, ',', ' ') ?> €)</td>
                            <td class="text-end"><?= number_format($commande['prix_sans_reduction'], 2, ',', ' ') ?> €</td>
                        </tr>
                        <?php if ($reduction > 0) { ?>
                            <tr>
                                <td>Réduction de 10 %</td>
                                <td class="text-end text-success">− <?= number_format($reduction, 2, ',', ' ') ?> €</td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>
                                Livraison
                                <?php if ($commande['prix_livraison'] > 0) { ?>
                                    (<?= number_format($commande['distance_km'], 1, ',', ' ') ?> km depuis Bordeaux)
                                <?php } ?>
                            </td>
                            <td class="text-end">
                                <?= $commande['prix_livraison'] > 0 ? number_format($commande['prix_livraison'], 2, ',', ' ') . ' €' : 'Offerte' ?>
                            </td>
                        </tr>
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-end"><?= number_format($total, 2, ',', ' ') ?> €</td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php if ($menu->getConditions()) { ?>
                <div class="alert alert-warning">
                    <strong>Conditions de ce menu :</strong><br>
                    <?= nl2br(htmlspecialchars($menu->getConditions())) ?>
                </div>
            <?php } ?>

            <form method="POST" action="index.php?controller=commande&action=confirm" class="d-flex gap-2 justify-content-end">
                <?php $lienModifier = isset($commande['numero_commande'])
                    ? 'index.php?controller=commande&action=modifier&numero=' . urlencode($commande['numero_commande'])
                    : 'index.php?controller=commande&action=create&menu_id=' . $menu->getMenuId(); ?>
                <a href="<?= $lienModifier ?>" class="btn btn-outline-secondary">Modifier</a>
                <button type="submit" name="confirmCommande" class="btn btn-primary">
                    <?= isset($commande['numero_commande']) ? 'Confirmer la modification' : 'Confirmer la commande' ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>