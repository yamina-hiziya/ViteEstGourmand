<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Horaire[] $horaires */
/** @var array $errors */
?>

<div class="container my-5">
    <?php $ongletActif = 'horaires';
    require _ROOTPATH_ . '/templates/employe/_onglets.php'; ?>

    <h1 class="mb-4">Horaires d'ouverture</h1>

    <?php if (!empty($succes)) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php } ?>
    <?php foreach ($errors as $error) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php } ?>

    <form method="POST">
        <div class="mb-3">
            <div class="row fw-bold border-bottom pb-2 mb-2 d-none d-md-flex">
                <div class="col-md-3">Jour</div>
                <div class="col-md-3">Ouverture</div>
                <div class="col-md-3">Fermeture</div>
                <div class="col-md-3">Fermé</div>
            </div>

            <?php foreach ($horaires as $horaire) {
                $id = $horaire->getHoraireId();
                $jour = $horaire->getJour();
                $ferme = $horaire->getHeureOuverture() === null;
            ?>
                <div class="row g-2 align-items-center border-bottom py-2">
                    <div class="col-12 col-md-3 fw-bold"><?= htmlspecialchars(ucfirst($jour)) ?></div>
                    <div class="col-5 col-md-3">
                        <input type="time" name="horaires[<?= $id ?>][ouverture]" class="form-control" value="<?= htmlspecialchars(substr($horaire->getHeureOuverture() ?? '', 0, 5)) ?>" aria-label="Ouverture <?= htmlspecialchars($jour) ?>">
                    </div>
                    <div class="col-5 col-md-3">
                        <input type="time" name="horaires[<?= $id ?>][fermeture]" class="form-control" value="<?= htmlspecialchars(substr($horaire->getHeureFermeture() ?? '', 0, 5)) ?>" aria-label="Fermeture <?= htmlspecialchars($jour) ?>">
                    </div>
                    <div class="col-2 col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="horaires[<?= $id ?>][ferme]" value="1" class="form-check-input" id="ferme_<?= $id ?>" <?= $ferme ? 'checked' : '' ?>>
                            <label class="form-check-label d-none d-md-inline" for="ferme_<?= $id ?>">Fermé</label>
                            <label class="form-check-label d-md-none visually-hidden" for="ferme_<?= $id ?>"><?= htmlspecialchars($jour) ?> fermé</label>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <button type="submit" name="saveHoraires" class="btn btn-primary">Enregistrer les horaires</button>
    </form>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>