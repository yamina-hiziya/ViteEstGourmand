<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Horaire[] $horaires */
/** @var array $errors */
?>

<div class="container my-5">
    <?php $ongletActif = 'horaires'; require _ROOTPATH_ . '/templates/employe/_onglets.php'; ?>

    <h1 class="mb-4">Horaires d'ouverture</h1>

    <?php if (!empty($succes)) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php } ?>
    <?php foreach ($errors as $error) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php } ?>

    <form method="POST">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Jour</th>
                        <th>Ouverture</th>
                        <th>Fermeture</th>
                        <th>Fermé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($horaires as $horaire) {
                        $id = $horaire->getHoraireId();
                        $ferme = $horaire->getHeureOuverture() === null;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars(ucfirst($horaire->getJour())) ?></td>
                            <td><input type="time" name="horaires[<?= $id ?>][ouverture]" class="form-control" value="<?= htmlspecialchars(substr($horaire->getHeureOuverture() ?? '', 0, 5)) ?>" aria-label="Ouverture <?= htmlspecialchars($horaire->getJour()) ?>"></td>
                            <td><input type="time" name="horaires[<?= $id ?>][fermeture]" class="form-control" value="<?= htmlspecialchars(substr($horaire->getHeureFermeture() ?? '', 0, 5)) ?>" aria-label="Fermeture <?= htmlspecialchars($horaire->getJour()) ?>"></td>
                            <td><input type="checkbox" name="horaires[<?= $id ?>][ferme]" value="1" class="form-check-input" <?= $ferme ? 'checked' : '' ?> aria-label="<?= htmlspecialchars($horaire->getJour()) ?> fermé"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <button type="submit" name="saveHoraires" class="btn btn-primary">Enregistrer les horaires</button>
    </form>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>