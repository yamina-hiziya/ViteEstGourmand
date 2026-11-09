<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Menu[] $menus 
 * @var App\Entity\Theme[] $themes
 * @var App\Entity\Regime[] $regimes
*/
?>
<div class="container my-5">
<form id="filtres-menus" class="row g-3 mb-4 align-items-end">
    <div class="col-6 col-md-2">
        <label for="prix_min" class="form-label">Prix min (€)</label>
        <input type="number" id="prix_min" name="prix_min" class="form-control" min="0" step="1">
    </div>

    <div class="col-6 col-md-2">
        <label for="prix_max" class="form-label">Prix max (€)</label>
        <input type="number" id="prix_max" name="prix_max" class="form-control" min="0" step="1">
    </div>

    <div class="col-12 col-md-3">
        <label for="theme_id" class="form-label">Thème</label>
        <select id="theme_id" name="theme_id" class="form-select">
            <option value="">Tous les thèmes</option>
            <?php foreach ($themes as $theme) { ?>
                <option value="<?= $theme->getThemeId() ?>"><?= htmlspecialchars($theme->getLibelle()) ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="col-12 col-md-3">
        <label for="regime_id" class="form-label">Régime</label>
        <select id="regime_id" name="regime_id" class="form-select">
            <option value="">Tous les régimes</option>
            <?php foreach ($regimes as $regime) { ?>
                <option value="<?= $regime->getRegimeId() ?>"><?= htmlspecialchars($regime->getLibelle()) ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="col-12 col-md-2">
        <label for="nb_personnes" class="form-label">Nb de personnes</label>
        <input type="number" id="nb_personnes" name="nb_personnes" class="form-control" min="1" step="1">
    </div>
</form>
    <div class="row justify-content-center" id="liste-menus">
    <?php require _ROOTPATH_ . '/templates/menus/_cards.php'; ?>
    </div>
</div>

<script src="/public/assets/js/filtres-menus.js"></script>
<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>