<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Plat $plat */
/** @var App\Entity\Allergene[] $allergenes */
/** @var array $allergenesCoches */
/** @var array $errors */
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <a href="index.php?controller=employe&action=plats" class="d-inline-block mb-3">← Tous les plats</a>
            <h1 class="mb-4"><?= $plat->getPlatId() ? 'Modifier le plat' : 'Ajouter un plat' ?></h1>

            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="titre_plat" class="form-label">Titre</label>
                    <input type="text" id="titre_plat" name="titre_plat" class="form-control" value="<?= htmlspecialchars($plat->getTitrePlat() ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="type_plat" class="form-label">Type</label>
                    <select id="type_plat" name="type_plat" class="form-select" required>
                        <option value="">Choisir</option>
                        <?php foreach (['entrée' => 'Entrée', 'plat' => 'Plat', 'dessert' => 'Dessert'] as $valeur => $libelle) { ?>
                            <option value="<?= $valeur ?>" <?= $plat->getTypePlat() === $valeur ? 'selected' : '' ?>><?= $libelle ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="photo" class="form-label">Photo (JPEG, PNG ou WebP, 2 Mo maximum)</label>
                    <?php if ($plat->getPhoto()) { ?>
                        <div class="mb-2">
                            <img src="/public/assets/images/plats/<?= htmlspecialchars($plat->getPhoto()) ?>" alt="Photo actuelle" style="width: 100px; height: 100px; object-fit: cover;" class="rounded">
                        </div>
                    <?php } ?>
                    <input type="file" id="photo" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>

                <fieldset class="mb-4">
                    <legend class="form-label fs-6">Allergènes</legend>
                    <div class="row">
                        <?php foreach ($allergenes as $allergene) {
                            $aid = $allergene->getAllergeneId();
                        ?>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="allergenes[]" value="<?= $aid ?>" id="allergene_<?= $aid ?>" <?= in_array($aid, $allergenesCoches) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="allergene_<?= $aid ?>"><?= htmlspecialchars($allergene->getLibelle()) ?></label>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </fieldset>

                <button type="submit" name="savePlat" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>
