<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\User $user */
/** @var array $errors */
$champs = [
    'nom' => ['Nom', $user->getNom()],
    'prenom' => ['Prénom', $user->getPrenom()],
    'telephone' => ['Téléphone', $user->getTelephone()],
    'adresse_postale' => ['Adresse postale', $user->getAdressePostale()],
    'ville' => ['Ville', $user->getVille()],
    'pays' => ['Pays', $user->getPays()],
];
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="index.php?controller=compte&action=commandes" class="btn btn-outline-primary">Mes commandes</a>
                <a href="index.php?controller=compte&action=profil" class="btn btn-primary">Mes informations</a>
            </div>

            <h1 class="mb-4">Mes informations</h1>

            <?php if (!empty($succes)) { ?>
                <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
            <?php } ?>

            <form method="POST" novalidate>
            <?= \App\Tools\Csrf::champ() ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" class="form-control" value="<?= htmlspecialchars($user->getEmail()) ?>" readonly>
                    <div class="form-text">L'email sert d'identifiant de connexion : il ne peut pas être modifié ici.</div>
                </div>

                <div class="row g-3 mb-3">
                    <?php foreach ($champs as $name => [$libelle, $valeur]) { ?>
                        <div class="col-md-6">
                            <label for="<?= $name ?>" class="form-label"><?= $libelle ?></label>
                            <input type="text" id="<?= $name ?>" name="<?= $name ?>" class="form-control <?= isset($errors[$name]) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($valeur ?? '') ?>" required>
                            <?php if (isset($errors[$name])) { ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors[$name]) ?></div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

                <button type="submit" name="saveProfil" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>