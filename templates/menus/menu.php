<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Menu $menu  */
/** @var App\Entity\Plat[] $plats  */
?>


<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h2 class="card-title text-center mb-3"><?= htmlspecialchars($menu->getTitre()) ?></h2>
                    <p class="card-text text-center mb-3"><?= htmlspecialchars($menu->getDescription())  ?></p>
                    <?php if (!empty($plats)) { ?>
                        <h5 class="mt-3">Plats proposés </h5>
                        <ul class="list-group mb-3">
                            <?php foreach ($plats as $plat) {
                                /** @var App\Entity\Plat $plat */
                            ?>
                                <li class="list-group-item d-flex align-items-center gap-3">
                                    <?php if ($plat->getPhoto()) { ?>
                                        <img src="/public/assets/images/plats/<?= htmlspecialchars($plat->getPhoto()) ?>" alt="<?= htmlspecialchars($plat->getTitrePlat()) ?>" style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                                    <?php } else { ?>
                                        <div class="rounded bg-light flex-shrink-0" style="width: 60px; height: 60px;"></div>
                                    <?php } ?>
                                    <div>
                                        <strong><?= htmlspecialchars(ucfirst($plat->getTypePlat())) ?> :</strong>
                                        <?= htmlspecialchars($plat->getTitrePlat()) ?>
                                        <?php if ($plat->getAllergenes()) { ?>
                                            <div class="small text-muted">Allergènes : <?= htmlspecialchars($plat->getAllergenes()) ?></div>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item"><strong>Thème :</strong> <?= htmlspecialchars($menu->getThemeLibelle()) ?></li>
                        <li class="list-group-item"><strong>Régime :</strong> <?= htmlspecialchars($menu->getRegimeLibelle()) ?></li>
                        <li class="list-group-item"><strong>Nombre de personnes minimal :</strong> <?= htmlspecialchars($menu->getNombrePersonneMinimum()) ?></li>
                        <li class="list-group-item"><strong>Prix par personne :</strong> <?= number_format($menu->getPrixParPersonne(), 2, ',', ' ') ?> €</li>
                    </ul>
                    <?php if ($menu->getConditions()) { ?>
                        <div class="alert alert-warning mb-3">
                            <strong>Conditions de ce menu :</strong><br>
                            <?= nl2br(htmlspecialchars($menu->getConditions())) ?>
                        </div>
                    <?php } ?>
                    <div class="d-flex justify-content-center">
                        <a href="index.php?controller=commande&action=create&menu_id=<?= $menu->getMenuId() ?>" class="btn btn-primary">Commander ce menu</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>