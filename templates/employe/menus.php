<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Menu[] $menus */
?>

<div class="container my-5">
    <?php $ongletActif = 'menus'; require _ROOTPATH_ . '/templates/employe/_onglets.php'; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="mb-0">Menus</h1>
        <a href="index.php?controller=employe&action=menu" class="btn btn-primary">Ajouter un menu</a>
    </div>

    <?php if (!empty($succes)) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php } ?>
    <?php if (!empty($erreur)) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php } ?>

    <div class="row g-4">
        <?php foreach ($menus as $menu) { ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <?php if ($menu->getPhoto()) { ?>
                        <img src="/public/assets/images/menu/<?= htmlspecialchars($menu->getPhoto()) ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?= htmlspecialchars($menu->getTitre()) ?>">
                    <?php } ?>
                    <div class="card-body d-flex flex-column">
                        <h2 class="h5 card-title"><?= htmlspecialchars($menu->getTitre()) ?></h2>
                        <p class="small text-muted mb-2">
                            <?= htmlspecialchars($menu->getThemeLibelle()) ?> — <?= htmlspecialchars($menu->getRegimeLibelle()) ?><br>
                            <?= number_format($menu->getPrixParPersonne(), 2, ',', ' ') ?> € / pers. — dès <?= $menu->getNombrePersonneMinimum() ?> pers.<br>
                            Stock : <strong class="<?= $menu->getQuantiteRestante() > 0 ? '' : 'text-danger' ?>"><?= $menu->getQuantiteRestante() ?></strong>
                        </p>
                        <div class="d-flex gap-2 mt-auto">
                            <a href="index.php?controller=employe&action=menu&id=<?= $menu->getMenuId() ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                            <form method="POST" action="index.php?controller=employe&action=supprimerMenu" onsubmit="return confirm('Supprimer ce menu ?');">
                                <input type="hidden" name="menu_id" value="<?= $menu->getMenuId() ?>">
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