<?php require_once _ROOTPATH_ . '/templates/header.php'; ?>

<div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <div class="row align-items-center g-lg-5 py-5">
        <div class="col-lg-7 text-center text-lg-start">
            <h1 class="display-4 fw-bold lh-1 text-body-emphasis mb-3">Connexion</h1>
            <p class="col-lg-10 fs-4">Veuillez vous connecter avec vos identifiants.</p>
        </div>
        <div class="col-md-10 mx-auto col-lg-5">
            <?php if (!empty($succes)) { ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($succes) ?>
                </div>
            <?php } ?>
            <?php foreach (($errors ?? []) as $error) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php } ?>

            <form class="p-4 p-md-5 border rounded-3 bg-body-tertiary" method="POST" action="index.php?controller=auth&action=login">
            <?= \App\Tools\Csrf::champ() ?>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?= htmlspecialchars($emailPrerempli ?? '') ?>" required>
                    <label for="email">Adresse e-mail</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                    <label for="password">Mot de passe</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="remember_email" name="remember_email" <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="remember_email">Se souvenir de mon email</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit" name="loginUser">Connexion</button>
                <div class="text-center mt-3">
                    <a href="index.php?controller=auth&action=oubli">Mot de passe oublié ?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>