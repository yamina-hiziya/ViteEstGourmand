<?php require_once _ROOTPATH_ . '/templates/header.php';

use App\Entity\Commande;

/** @var array $commandes */
/** @var string $statut */
/** @var string $client */
?>

<div class="container my-5">
    <?php $ongletActif = 'commandes';
    require _ROOTPATH_ . '/templates/employe/_onglets.php'; ?>
    <h1 class="mb-4">Gestion des commandes</h1>

    <form method="GET" class="row g-3 mb-4 align-items-end">
        <input type="hidden" name="controller" value="employe">
        <input type="hidden" name="action" value="commandes">

        <div class="col-md-4">
            <label for="statut" class="form-label">Statut</label>
            <select id="statut" name="statut" class="form-select">
                <option value="">Tous les statuts</option>
                <?php foreach (Commande::STATUTS as $s) { ?>
                    <option value="<?= htmlspecialchars($s) ?>" <?= $s === $statut ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($s)) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-5">
            <label for="client" class="form-label">Client (nom, prénom ou email)</label>
            <input type="text" id="client" name="client" class="form-control" value="<?= htmlspecialchars($client) ?>">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="index.php?controller=employe&action=commandes" class="btn btn-outline-secondary">Réinitialiser</a>
        </div>
    </form>

    <?php if (empty($commandes)) { ?>
        <p>Aucune commande ne correspond à votre recherche.</p>
    <?php } else { ?>
        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>N° de commande</th>
                        <th>Client</th>
                        <th class="d-none d-md-table-cell">Menu</th>
                        <th>Prestation</th>
                        <th class="d-none d-md-table-cell">Total</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $c) { ?>
                        <tr>
                            <td><a href="index.php?controller=employe&action=commande&numero=<?= urlencode($c['numero_commande']) ?>"><?= htmlspecialchars($c['numero_commande']) ?></a></td>
                            <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?><br><small class="text-muted"><?= htmlspecialchars($c['email']) ?></small></td>
                            <td class="d-none d-md-table-cell"><?= htmlspecialchars($c['menu_titre']) ?></td>
                            <td><?= date('d/m/Y', strtotime($c['date_prestation'])) ?></td>
                            <td class="d-none d-md-table-cell"><?= number_format($c['prix_menu'] + $c['prix_livraison'], 2, ',', ' ') ?> €</td>
                            <td><?= htmlspecialchars(ucfirst($c['statut'])) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="d-md-none">
            <?php foreach ($commandes as $c) { ?>
                <a href="index.php?controller=employe&action=commande&numero=<?= urlencode($c['numero_commande']) ?>" class="card mb-3 text-decoration-none text-reset">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <strong class="small"><?= htmlspecialchars($c['numero_commande']) ?></strong>
                            <span class="badge text-bg-secondary"><?= htmlspecialchars(ucfirst($c['statut'])) ?></span>
                        </div>
                        <div><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></div>
                        <div class="small text-muted"><?= htmlspecialchars($c['menu_titre']) ?> — le <?= date('d/m/Y', strtotime($c['date_prestation'])) ?></div>
                    </div>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>