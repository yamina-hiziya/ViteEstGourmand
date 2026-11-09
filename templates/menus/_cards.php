<?php
/** @var App\Entity\Menu[] $menus */
?>
<?php foreach ($menus as $menu) {
    /** @var App\Entity\Menu $menu */
?>
    <div class="col-md-4 my-2">
        <div class="card w-100 h-100">
            <img src="/public/assets/images/menu/<?= htmlspecialchars($menu->getPhoto()) ?>" class="card-img-top" style="height: 220px; object-fit: cover; object-position: center 42%;" alt="<?= htmlspecialchars($menu->getTitre()) ?>">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?= htmlspecialchars($menu->getTitre()) ?></h5>
                <p class="card-text"><?= htmlspecialchars($menu->getDescription()) ?></p>
                <ul class="list-unstyled mb-3">
                    <li><strong>À partir de :</strong> <?= $menu->getNombrePersonneMinimum() ?> personnes</li>
                    <li><strong>Prix :</strong> <?= number_format($menu->getPrixParPersonne(), 2, ',', ' ') ?> € par personne</li>
                </ul>
                <a href="index.php?controller=menu&action=show&id=<?= $menu->getMenuId() ?>" class="btn btn-primary mt-auto">Voir la fiche</a>
            </div>
        </div>
    </div>
<?php } ?>