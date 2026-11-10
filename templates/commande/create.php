<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\Menu $menu */
/** @var App\Entity\Plat[] $plats */
/** @var App\Entity\User $user */
/** @var array $errors */
$types = ['entrée' => 'Entrées', 'plat' => 'Plats', 'dessert' => 'Desserts'];
?>

<div class="container my-5">
    <h1 class="mb-2">Commander : <?= htmlspecialchars($menu->getTitre()) ?></h1>
    <p class="text-muted mb-4">
        À partir de <?= $menu->getNombrePersonneMinimum() ?> personnes —
        <?= number_format($menu->getPrixParPersonne(), 2, ',', ' ') ?> € par personne
    </p>
    <?php foreach (($errors ?? []) as $error) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php } ?>

    <form method="POST" novalidate>

        <h2 class="h5 mt-4">Vos informations</h2>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label" for="nom_complet">Nom et prénom</label>
                <input type="text" id="nom_complet" class="form-control" value="<?= htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()) ?>" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="email_client">Email</label>
                <input type="email" id="email_client" class="form-control" value="<?= htmlspecialchars($user->getEmail()) ?>" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="telephone_client">Téléphone</label>
                <input type="tel" id="telephone_client" class="form-control" value="<?= htmlspecialchars($user->getTelephone()) ?>" readonly>
            </div>
        </div>

        <h2 class="h5 mt-4">La prestation</h2>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label" for="adresse_livraison">Adresse de la prestation</label>
                <input type="text" id="adresse_livraison" name="adresse_livraison" class="form-control" value="<?= htmlspecialchars($_POST['adresse_livraison'] ?? $user->getAdressePostale()) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="ville_livraison">Ville</label>
                <input type="text" id="ville_livraison" name="ville_livraison" class="form-control" value="<?= htmlspecialchars($_POST['ville_livraison'] ?? $user->getVille()) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="date_prestation">Date de la prestation</label>
                <input type="date" id="date_prestation" name="date_prestation" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($_POST['date_prestation'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="heure_livraison">Heure de livraison souhaitée</label>
                <input type="time" id="heure_livraison" name="heure_livraison" class="form-control" value="<?= htmlspecialchars($_POST['heure_livraison'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="nombre_personne">Nombre de personnes</label>
                <input type="number" id="nombre_personne" name="nombre_personne" class="form-control" min="<?= $menu->getNombrePersonneMinimum() ?>" value="<?= htmlspecialchars($_POST['nombre_personne'] ?? $menu->getNombrePersonneMinimum()) ?>" required>
            </div>
        </div>

        <h2 class="h5 mt-4">Le choix des plats</h2>
        <p class="small text-muted">Pour chaque partie du repas, répartissez le nombre de personnes entre les choix proposés.</p>

        <?php foreach ($types as $type => $titreType) { ?>
            <h3 class="h6 mt-3"><?= $titreType ?></h3>
            <div class="row g-3 mb-2">
                <?php foreach ($plats as $plat) {
                    if ($plat->getTypePlat() !== $type) {
                        continue;
                    }
                ?>
                    <div class="col-md-6">
                        <label class="form-label" for="quantite_<?= $plat->getPlatId() ?>"><?= htmlspecialchars($plat->getTitrePlat()) ?></label>
                        <input type="number" id="quantite_<?= $plat->getPlatId() ?>" name="quantites[<?= $plat->getPlatId() ?>]" class="form-control" min="0" value="<?= htmlspecialchars($_POST['quantites'][$plat->getPlatId()] ?? 0) ?>">
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($menu->getConditions()) { ?>
            <div class="alert alert-warning mt-4">
                <strong>Conditions de ce menu :</strong><br>
                <?= nl2br(htmlspecialchars($menu->getConditions())) ?>
            </div>
        <?php } ?>

        <button type="submit" name="saveCommande" class="btn btn-primary btn-lg mt-3">Valider ma commande</button>
    </form>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>