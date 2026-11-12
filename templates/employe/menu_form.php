<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Menu $menu */
/** @var App\Entity\Theme[] $themes */
/** @var App\Entity\Regime[] $regimes */
/** @var App\Entity\Plat[] $plats */
/** @var array $platsCoches */
/** @var array $errors */
$types = ['entrée' => 'Entrées', 'plat' => 'Plats', 'dessert' => 'Desserts'];
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <a href="index.php?controller=employe&action=menus" class="d-inline-block mb-3">← Tous les menus</a>
            <h1 class="mb-4"><?= $menu->getMenuId() ? 'Modifier le menu' : 'Ajouter un menu' ?></h1>

            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" id="titre" name="titre" class="form-control" value="<?= htmlspecialchars($menu->getTitre() ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required><?= htmlspecialchars($menu->getDescription() ?? '') ?></textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="theme_id" class="form-label">Thème</label>
                        <select id="theme_id" name="theme_id" class="form-select" required>
                            <option value="">Choisir</option>
                            <?php foreach ($themes as $theme) { ?>
                                <option value="<?= $theme->getThemeId() ?>" <?= $menu->getThemeId() === $theme->getThemeId() ? 'selected' : '' ?>><?= htmlspecialchars($theme->getLibelle()) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="regime_id" class="form-label">Régime</label>
                        <select id="regime_id" name="regime_id" class="form-select" required>
                            <option value="">Choisir</option>
                            <?php foreach ($regimes as $regime) { ?>
                                <option value="<?= $regime->getRegimeId() ?>" <?= $menu->getRegimeId() === $regime->getRegimeId() ? 'selected' : '' ?>><?= htmlspecialchars($regime->getLibelle()) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="prix_par_personne" class="form-label">Prix par personne (€)</label>
                        <input type="number" id="prix_par_personne" name="prix_par_personne" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars((string) ($menu->getPrixParPersonne() ?? '')) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="nombre_personne_minimum" class="form-label">Personnes minimum</label>
                        <input type="number" id="nombre_personne_minimum" name="nombre_personne_minimum" class="form-control" min="1" value="<?= htmlspecialchars((string) ($menu->getNombrePersonneMinimum() ?? '')) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="quantite_restante" class="form-label">Stock (commandes possibles)</label>
                        <input type="number" id="quantite_restante" name="quantite_restante" class="form-control" min="0" value="<?= htmlspecialchars((string) ($menu->getQuantiteRestante() ?? '')) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="conditions" class="form-label">Conditions (délai de commande, conservation…)</label>
                    <textarea id="conditions" name="conditions" class="form-control" rows="2"><?= htmlspecialchars($menu->getConditions() ?? '') ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="photo" class="form-label">Photo (JPEG, PNG ou WebP, 2 Mo maximum)</label>
                    <?php if ($menu->getPhoto()) { ?>
                        <div class="mb-2">
                            <img src="/public/assets/images/menu/<?= htmlspecialchars($menu->getPhoto()) ?>" alt="Photo actuelle" style="width: 150px; height: 100px; object-fit: cover;" class="rounded">
                        </div>
                    <?php } ?>
                    <input type="file" id="photo" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>

                <h2 class="h5">Plats proposés</h2>
                <p class="small text-muted">Au moins une entrée, un plat et un dessert. Deux choix par type sont recommandés.</p>

                <?php foreach ($types as $type => $titreType) { ?>
                    <fieldset class="mb-3">
                        <legend class="form-label fs-6 fw-bold"><?= $titreType ?></legend>
                        <div class="row">
                            <?php foreach ($plats as $plat) {
                                if ($plat->getTypePlat() !== $type) {
                                    continue;
                                }
                                $pid = $plat->getPlatId();
                            ?>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="plats[]" value="<?= $pid ?>" id="plat_<?= $pid ?>" <?= in_array($pid, $platsCoches) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="plat_<?= $pid ?>"><?= htmlspecialchars($plat->getTitrePlat()) ?></label>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </fieldset>
                <?php } ?>

                <button type="submit" name="saveMenu" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>