<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var array $errors */
/** @var bool $valide */
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <h1 class="mb-3">Nouveau mot de passe</h1>

            <?php if (!$valide) { ?>
                <div class="alert alert-danger">Ce lien n'est plus valable : il a expiré ou a déjà été utilisé.</div>
                <a href="index.php?controller=auth&action=oubli">Demander un nouveau lien</a>
            <?php } else { ?>
                <form method="POST" novalidate>
                <?= \App\Tools\Csrf::champ() ?>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required aria-describedby="aidePassword">
                        <?php if (isset($errors['password'])) { ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                        <?php } ?>
                        <div id="aidePassword" class="form-text">10 caractères minimum, dont une majuscule, une minuscule, un chiffre et un caractère spécial.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" required>
                        <?php if (isset($errors['password_confirm'])) { ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['password_confirm']) ?></div>
                        <?php } ?>
                    </div>
                    <button type="submit" name="saveMotDePasse" class="btn btn-primary w-100">Enregistrer</button>
                </form>
            <?php } ?>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>