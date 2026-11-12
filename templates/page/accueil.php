<?php require_once _ROOTPATH_ . '/templates/header.php' ?>

<div class="px-4 pt-5 pb-3 mb-0 text-center"> <img class="d-block mx-auto mb-4 rounded-5 img-fluid" src="/public/assets/images/logo_vite_gourmand_degrade_premium.svg" alt="Logo Vite & Gourmand" width="450">
    <div class="col-lg-6 mx-auto">
        <p class="lead mb-4">Votre traiteur en ligne pour tous vos événements (mariages, anniversaires, réceptions...)</p>
    </div>
</div>
<div class="container col-xxl-8 px-4 pt-0 pb-5">
    <div class="row flex-lg-row-reverse align-items-center g-5 pb-5">
        <div class="col-12 col-lg-6"> <img src="/public/assets/images/julie-jose.jpg" class="d-block mx-auto img-fluid rounded-3" alt="José et Julie préparant un buffet dans leur cuisine" width="700" height="500" loading="lazy"> </div>
        <div class="col-lg-6">
            <h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3">Présentation</h1>
            <p class="lead">Nous sommes José et Julie, passionnés de cuisine et de convivialité, situés à Bordeaux. Notre mission : vous offrir des plats faits maison, savoureux et adaptés à tous vos événements. Que ce soit pour un mariage, un anniversaire ou une réception, nous mettons tout notre savoir-faire pour régaler vos invités et rendre votre moment inoubliable.</p>
        </div>
    </div>
</div>
<div class="container px-4 py-5" id="featured-3">
    <h2 class="pb-2 border-bottom text-center"> ★ Menus ★ </h2>
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
        <div class="feature col text-center">
            <div class="feature-icon mb-3">🥗</div>
            <h3 class="fs-2 text-body-emphasis text-center"> ★ Entrées</h3>
            <p>Des entrées fraîches et savoureuses pour bien commencer votre repas.</p> <a href="#" class="icon-link">
                Call to action
                <svg class="bi" aria-hidden="true">
                    <use xlink:href="#chevron-right"></use>
                </svg> </a>
        </div>
        <div class="feature col text-center">
            <div class="feature-icon mb-3">🍖</div>
            <h3 class="fs-2 text-body-emphasis text-center"> ★ Plats</h3>
            <p>Des plats généreux préparés avec des produits de qualité.</p> <a href="#" class="icon-link">
                Call to action
                <svg class="bi" aria-hidden="true">
                    <use xlink:href="#chevron-right"></use>
                </svg> </a>
        </div>
        <div class="feature col text-center">
            <div class="feature-icon mb-3">🍰</div>
            <h3 class="fs-2 text-body-emphasis text-center"> ★ Desserts</h3>
            <p>Des desserts gourmands pour terminer en beauté.</p> <a href="#" class="icon-link">
                Call to action
                <svg class="bi" aria-hidden="true">
                    <use xlink:href="#chevron-right"></use>
                </svg> </a>
        </div>
    </div>
    <div class="d-grid gap-2 d-md-flex justify-content-md-center"> <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">Découvrir notre carte</button></div>
</div>
<?php if (!empty($avisValides)) { ?>
    <section class="container my-5">
        <h2 class="text-center mb-4">Ils nous ont fait confiance</h2>
        <div class="row g-4 justify-content-center">
            <?php foreach ($avisValides as $avis) { ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="text-warning mb-2"><?= str_repeat('★', $avis['note']) . str_repeat('☆', 5 - $avis['note']) ?></p>
                            <p><?= nl2br(htmlspecialchars($avis['description'])) ?></p>
                            <p class="small text-muted mb-0">— <?= htmlspecialchars(ucfirst($avis['prenom'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
<?php } ?>

<?php require_once _ROOTPATH_ . '/templates/footer.php' ?>