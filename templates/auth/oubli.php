<?php require_once _ROOTPATH_ . '/templates/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <h1 class="mb-3">Mot de passe oublié</h1>

            <?php if ($envoye) { ?>
                <div class="alert alert-success">Si un compte existe avec cette adresse, un lien de réinitialisation vous a été envoyé. Pensez à vérifier vos courriers indésirables.</div>
                <a href="index.php?controller=auth&action=login">Retour à la connexion</a>
            <?php } else { ?>
                <p>Indiquez l'adresse email de votre compte : nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>
                <form method="POST">
                    <div class="form-floating mb-3">
                        <input type="email" id="email" name="email" class="form-control" placeholder="nom@exemple.com" required>
                        <label for="email">Adresse email</label>
                    </div>
                    <button type="submit" name="demanderLien" class="btn btn-primary w-100">Recevoir le lien</button>
                </form>
            <?php } ?>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>