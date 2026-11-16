<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var array $statistiques */
/** @var App\Entity\Menu[] $menus */
/** @var int|null $menuId */
/** @var string|null $debut */
/** @var string|null $fin */
/** @var int $totalCommandes */
/** @var float $totalChiffreAffaires */
/** @var string|null $erreur */
/** @var string|null $succes */
?>

<div class="container my-5">
    <?php $ongletActif = 'statistiques'; require _ROOTPATH_ . '/templates/admin/_onglets.php'; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="mb-0">Statistiques</h1>
        <form method="POST" action="index.php?controller=admin&action=synchroniser">
            <button type="submit" name="synchroniser" class="btn btn-outline-secondary">Synchroniser avec MongoDB</button>
        </form>
    </div>

    <?php if (!empty($succes)) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php } ?>
    <?php if (!empty($erreur)) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php } ?>

    <form method="GET" class="row g-3 mb-4 align-items-end">
        <input type="hidden" name="controller" value="admin">
        <input type="hidden" name="action" value="statistiques">
        <div class="col-md-4">
            <label for="menu_id" class="form-label">Menu</label>
            <select id="menu_id" name="menu_id" class="form-select">
                <option value="">Tous les menus</option>
                <?php foreach ($menus as $menu) { ?>
                    <option value="<?= $menu->getMenuId() ?>" <?= $menuId === $menu->getMenuId() ? 'selected' : '' ?>><?= htmlspecialchars($menu->getTitre()) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label for="debut" class="form-label">Du</label>
            <input type="date" id="debut" name="debut" class="form-control" value="<?= htmlspecialchars($debut ?? '') ?>">
        </div>
        <div class="col-6 col-md-3">
            <label for="fin" class="form-label">Au</label>
            <input type="date" id="fin" name="fin" class="form-control" value="<?= htmlspecialchars($fin ?? '') ?>">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="index.php?controller=admin&action=statistiques" class="btn btn-outline-secondary">×</a>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="small text-muted">Commandes</div>
                    <div class="fs-3 fw-bold"><?= $totalCommandes ?></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="small text-muted">Chiffre d'affaires</div>
                    <div class="fs-3 fw-bold"><?= number_format($totalChiffreAffaires, 2, ',', ' ') ?> €</div>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($statistiques)) { ?>
        <p>Aucune commande sur cette période.</p>
    <?php } else { ?>
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5">Nombre de commandes par menu</h2>
                <canvas id="graphique" height="120" aria-label="Graphique du nombre de commandes par menu" role="img"></canvas>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th class="text-end">Commandes</th>
                        <th class="text-end">Chiffre d'affaires</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statistiques as $ligne) { ?>
                        <tr>
                            <td><?= htmlspecialchars($ligne['menu']) ?></td>
                            <td class="text-end"><?= $ligne['nb_commandes'] ?></td>
                            <td class="text-end"><?= number_format($ligne['chiffre_affaires'], 2, ',', ' ') ?> €</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
        <script>
            const donnees = <?= json_encode($statistiques, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

            new Chart(document.getElementById('graphique'), {
                type: 'bar',
                data: {
                    labels: donnees.map(ligne => ligne.menu),
                    datasets: [{
                        label: 'Nombre de commandes',
                        data: donnees.map(ligne => ligne.nb_commandes),
                        backgroundColor: '#1B4A8A',
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        </script>
    <?php } ?>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>