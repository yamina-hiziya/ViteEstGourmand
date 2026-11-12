<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Plat[] $plats */
?>

<div class="container my-5">
    <?php $ongletActif = 'plats';
    require _ROOTPATH_ . '/templates/employe/_onglets.php'; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="mb-0">Plats</h1>
        <a href="index.php?controller=employe&action=plat" class="btn btn-primary">Ajouter un plat</a>
    </div>

    <?php if (!empty($succes)) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php } ?>
    <?php if (!empty($erreur)) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php } ?>

    <div class="table-responsive d-none d-md-block">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Titre</th>
                    <th>Type</th>
                    <th class="d-none d-md-table-cell">Allergènes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plats as $plat) { ?>
                    <tr>
                        <td>
                            <?php if ($plat->getPhoto()) { ?>
                                <img src="/public/assets/images/plats/<?= htmlspecialchars($plat->getPhoto()) ?>" alt="<?= htmlspecialchars($plat->getTitrePlat()) ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                            <?php } ?>
                        </td>
                        <td><?= htmlspecialchars($plat->getTitrePlat()) ?></td>
                        <td><?= htmlspecialchars(ucfirst($plat->getTypePlat())) ?></td>
                        <td class="small d-none d-md-table-cell"><?= htmlspecialchars($plat->getAllergenes() ?? '—') ?></td>
                        <td>
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <a href="index.php?controller=employe&action=plat&id=<?= $plat->getPlatId() ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                                <form method="POST" action="index.php?controller=employe&action=supprimerPlat" onsubmit="return confirm('Supprimer ce plat ?');">
                                    <input type="hidden" name="plat_id" value="<?= $plat->getPlatId() ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="d-md-none">
        <?php foreach ($plats as $plat) { ?>
            <div class="card mb-3">
                <div class="card-body d-flex gap-3">
                    <?php if ($plat->getPhoto()) { ?>
                        <img src="/public/assets/images/plats/<?= htmlspecialchars($plat->getPhoto()) ?>" alt="<?= htmlspecialchars($plat->getTitrePlat()) ?>" style="width: 60px; height: 60px; object-fit: cover;" class="rounded flex-shrink-0">
                    <?php } else { ?>
                        <div class="rounded bg-light flex-shrink-0" style="width: 60px; height: 60px;"></div>
                    <?php } ?>
                    <div class="flex-grow-1">
                        <strong><?= htmlspecialchars($plat->getTitrePlat()) ?></strong>
                        <div class="small text-muted"><?= htmlspecialchars(ucfirst($plat->getTypePlat())) ?></div>
                        <?php if ($plat->getAllergenes()) { ?>
                            <div class="small text-muted">Allergènes : <?= htmlspecialchars($plat->getAllergenes()) ?></div>
                        <?php } ?>
                        <div class="d-flex gap-2 mt-2">
                            <a href="index.php?controller=employe&action=plat&id=<?= $plat->getPlatId() ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                            <form method="POST" action="index.php?controller=employe&action=supprimerPlat" onsubmit="return confirm('Supprimer ce plat ?');">
                                <input type="hidden" name="plat_id" value="<?= $plat->getPlatId() ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>