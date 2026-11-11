<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var array $commandes */
$couleurs = [
    'en attente' => 'secondary',
    'accepté' => 'primary',
    'en préparation' => 'info',
    'en cours de livraison' => 'info',
    'livré' => 'success',
    'en attente du retour de matériel' => 'warning',
    'terminée' => 'success',
    'annulée' => 'danger',
];
?>

<div class="container my-5">
    <h1 class="mb-4">Mes commandes</h1>

    <?php if (empty($commandes)) { ?>
        <p>Vous n'avez pas encore passé de commande.</p>
        <a href="index.php?controller=menu&action=list" class="btn btn-primary">Découvrir nos menus</a>
    <?php } else { ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>N° de commande</th>
                        <th>Menu</th>
                        <th>Prestation</th>
                        <th>Total</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande) { ?>
                        <tr>
                            <td><a href="index.php?controller=compte&action=detail&numero=<?= urlencode($commande['numero_commande']) ?>"><?= htmlspecialchars($commande['numero_commande']) ?></a></td>
                            <td><?= htmlspecialchars($commande['menu_titre']) ?></td>
                            <td><?= date('d/m/Y', strtotime($commande['date_prestation'])) ?></td>
                            <td><?= number_format($commande['prix_menu'] + $commande['prix_livraison'], 2, ',', ' ') ?> €</td>
                            <td>
                                <span class="badge text-bg-<?= $couleurs[$commande['statut']] ?? 'secondary' ?>">
                                    <?= htmlspecialchars(ucfirst($commande['statut'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>