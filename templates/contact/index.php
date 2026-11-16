<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var array $errors */
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <h1 class="mb-2">Nous contacter</h1>
            <p class="mb-4">Une question sur un menu, une demande particulière pour votre événement ? Écrivez-nous, nous vous répondrons rapidement.</p>

            <?php if (!empty($succes)) { ?>
                <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
            <?php } ?>
            <?php if (isset($errors['general'])) { ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
            <?php } ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" id="titre" name="titre" maxlength="150" class="form-control <?= isset($errors['titre']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>
                    <?php if (isset($errors['titre'])) { ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['titre']) ?></div>
                    <?php } ?>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Votre message</label>
                    <textarea id="message" name="message" rows="6" class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    <?php if (isset($errors['message'])) { ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['message']) ?></div>
                    <?php } ?>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Votre email</label>
                    <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['email'] ?? ($_SESSION['user']['email'] ?? '')) ?>" required>
                    <?php if (isset($errors['email'])) { ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php } ?>
                </div>

                <!-- Champ piège anti-robot : caché pour les humains et les lecteurs d'écran -->
                <div class="d-none" aria-hidden="true">
                    <label for="site_web">Ne pas remplir ce champ</label>
                    <input type="text" id="site_web" name="site_web" tabindex="-1" autocomplete="off">
                </div>

                <button type="submit" name="envoyerContact" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>