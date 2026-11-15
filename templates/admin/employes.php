<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var App\Entity\User[] $employes */
/** @var App\Entity\User $nouveau */
/** @var array $errors */
?>

<div class="container my-5">
    <h1 class="mb-4">Comptes employés</h1>

    <?php if (!empty($succes)) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php } ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <?php if (empty($employes)) { ?>
                <p>Aucun employé pour le moment.</p>
            <?php } ?>
            <?php foreach ($employes as $employe) { ?>
                <div class="card mb-3">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <strong><?= htmlspecialchars($employe->getPrenom() . ' ' . $employe->getNom()) ?></strong>
                            <span class="badge <?= $employe->isActif() ? 'text-bg-success' : 'text-bg-secondary' ?> ms-2"><?= $employe->isActif() ? 'Actif' : 'Désactivé' ?></span>
                            <div class="small text-muted"><?= htmlspecialchars($employe->getEmail()) ?></div>
                        </div>
                        <form method="POST" action="index.php?controller=admin&action=activer">
                            <input type="hidden" name="utilisateur_id" value="<?= $employe->getUtilisateurId() ?>">
                            <?php if ($employe->isActif()) { ?>
                                <input type="hidden" name="actif" value="0">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Désactiver ce compte ?');">Désactiver</button>
                            <?php } else { ?>
                                <input type="hidden" name="actif" value="1">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Réactiver</button>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="h5">Créer un compte employé</h2>
                    <form method="POST" novalidate>
                        <?php foreach (['nom' => 'Nom', 'prenom' => 'Prénom', 'email' => 'Email'] as $name => $libelle) {
                            $getter = 'get' . ucfirst($name);
                        ?>
                            <div class="mb-3">
                                <label for="<?= $name ?>" class="form-label"><?= $libelle ?></label>
                                <input type="<?= $name === 'email' ? 'email' : 'text' ?>" id="<?= $name ?>" name="<?= $name ?>" class="form-control <?= isset($errors[$name]) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($nouveau->$getter() ?? '') ?>" required>
                                <?php if (isset($errors[$name])) { ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors[$name]) ?></div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required>
                            <?php if (isset($errors['password'])) { ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                            <?php } ?>
                            <div class="form-text">10 caractères minimum, dont une majuscule, une minuscule, un chiffre et un caractère spécial.</div>
                        </div>
                        <button type="submit" name="creerEmploye" class="btn btn-primary">Créer le compte</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>