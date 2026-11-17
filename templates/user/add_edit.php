<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var string $pageTitle */
/** @var array $errors */
?>

<div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center mb-4">
            <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="fs-5">Veuillez remplir le formulaire ci-dessous pour vous inscrire.</p>
        </div>

        <div class="col-12 col-lg-10 col-xl-9">
            <form method="POST" class="p-4 p-md-5 border rounded-3 bg-body-tertiary" novalidate>
            <?= \App\Tools\Csrf::champ() ?>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" id="nom" name="nom" placeholder="Nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                            <label for="nom">Nom</label>
                            <?php if (isset($errors['nom'])) { ?>
                                <div class="invalid-feedback"><?= $errors['nom'] ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>" id="prenom" name="prenom" placeholder="Prénom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                            <label for="prenom">Prénom</label>
                            <?php if (isset($errors['prenom'])) { ?>
                                <div class="invalid-feedback"><?= $errors['prenom'] ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" placeholder="nom@exemple.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            <label for="email">Email</label>
                            <?php if (isset($errors['email'])) { ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="tel" class="form-control <?= isset($errors['telephone']) ? 'is-invalid' : '' ?>" id="telephone" name="telephone" placeholder="Téléphone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" required>
                            <label for="telephone">Téléphone</label>
                            <?php if (isset($errors['telephone'])) { ?>
                                <div class="invalid-feedback"><?= $errors['telephone'] ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" class="form-control <?= isset($errors['adresse_postale']) ? 'is-invalid' : '' ?>" id="adresse_postale" name="adresse_postale" placeholder="Adresse postale" value="<?= htmlspecialchars($_POST['adresse_postale'] ?? '') ?>" required>
                            <label for="adresse_postale">Adresse postale</label>
                            <?php if (isset($errors['adresse_postale'])) { ?>
                                <div class="invalid-feedback"><?= $errors['adresse_postale'] ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control <?= isset($errors['ville']) ? 'is-invalid' : '' ?>" id="ville" name="ville" placeholder="Ville" value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>" required>
                            <label for="ville">Ville</label>
                            <?php if (isset($errors['ville'])) { ?>
                                <div class="invalid-feedback"><?= $errors['ville'] ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control <?= isset($errors['pays']) ? 'is-invalid' : '' ?>" id="pays" name="pays" placeholder="Pays" value="<?= htmlspecialchars($_POST['pays'] ?? 'France') ?>" required>
                            <label for="pays">Pays</label>
                            <?php if (isset($errors['pays'])) { ?>
                                <div class="invalid-feedback"><?= $errors['pays'] ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group has-validation">
                            <div class="form-floating <?= isset($errors['password']) ? 'is-invalid' : '' ?>">
                                <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Mot de passe" required minlength="10" aria-describedby="aidePassword">
                                <label for="password">Mot de passe</label>
                            </div>
                            <button type="button" class="btn btn-outline-secondary" data-cible="password_confirm" aria-label="Afficher le mot de passe" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
                            <?php if (isset($errors['password'])) { ?>
                                <div class="invalid-feedback"><?= $errors['password'] ?></div>
                            <?php } ?>
                        </div>
                        <div id="aidePassword" class="form-text">10 caractères minimum, dont une majuscule, une minuscule, un chiffre et un caractère spécial.</div>
                    </div>

                    <div class="col-12">
                        <div class="input-group has-validation">
                            <div class="form-floating <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>">
                                <input type="password" class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" id="password_confirm" name="password_confirm" placeholder="Confirmer le mot de passe" required>
                                <label for="password_confirm">Confirmer le mot de passe</label>
                            </div>
                            <button type="button" class="btn btn-outline-secondary" data-cible="password_confirm" aria-label="Afficher le mot de passe" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
                            <?php if (isset($errors['password_confirm'])) { ?>
                                <div class="invalid-feedback"><?= $errors['password_confirm'] ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input <?= isset($errors['consentement']) ? 'is-invalid' : '' ?>" type="checkbox" value="1" id="consentement" name="consentement" required>
                    <label class="form-check-label" for="consentement">J'accepte que mes données personnelles soient utilisées pour la gestion de mon compte et de mes commandes.</label>
                    <?php if (isset($errors['consentement'])) { ?>
                        <div class="invalid-feedback"><?= $errors['consentement'] ?></div>
                    <?php } ?>
                </div>

                <button class="w-100 btn btn-lg btn-primary" type="submit" name="saveUser">Inscription</button>
                <hr class="my-4">
                <small class="text-body-secondary">En cliquant sur Inscription, vous acceptez les conditions d'utilisation.</small>
            </form>
        </div>
    </div>
</div>
<script src="/public/assets/js/afficher-mdp.js"></script>
<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>