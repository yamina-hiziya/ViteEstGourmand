<?php require_once _ROOTPATH_ . '/templates/header.php' 
/** @var array $avisValides */
/** @var App\Entity\Menu[] $menusALaUne */
?>

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
<section class="container px-4 py-5">
    <h2 class="pb-2 border-bottom text-center">Pour tous vos événements</h2>
    <div class="row g-4 py-4 row-cols-2 row-cols-lg-4">
        <?php
        $evenements = [
            ['icone' => 'bi-heart', 'titre' => 'Mariages', 'texte' => 'Des menus raffinés pour le plus beau jour.', 'theme' => 3],
            ['icone' => 'bi-gift', 'titre' => 'Anniversaires', 'texte' => 'Des repas conviviaux à partager entre proches.', 'theme' => 4],
            ['icone' => 'bi-briefcase', 'titre' => 'Entreprises', 'texte' => 'Séminaires, repas d\'équipe et réceptions.', 'theme' => 5],
            ['icone' => 'bi-stars', 'titre' => 'Fêtes de fin d\'année', 'texte' => 'Des menus de Noël pour régaler toute la famille.', 'theme' => 1],
        ];
        foreach ($evenements as $evenement) { ?>
            <div class="col">
                <a href="index.php?controller=menu&action=list&theme_id=<?= $evenement['theme'] ?>" class="card h-100 text-center text-decoration-none text-reset">
                    <div class="card-body">
                        <i class="bi <?= $evenement['icone'] ?> feature-icon d-block mb-2" aria-hidden="true"></i>
                        <h3 class="fs-5"><?= $evenement['titre'] ?></h3>
                        <p class="small mb-0"><?= $evenement['texte'] ?></p>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
</section>

<?php if (!empty($menusALaUne)) { ?>
    <section class="container px-4 pb-5">
        <h2 class="pb-2 border-bottom text-center">Nos menus du moment</h2>
        <div class="row justify-content-center py-4">
            <?php
            $menus = $menusALaUne;
            require _ROOTPATH_ . '/templates/menus/_cards.php';
            ?>
        </div>
        <div class="text-center">
            <a href="index.php?controller=menu&action=list" class="btn btn-primary btn-lg px-4">Découvrir tous nos menus</a>
        </div>
    </section>
<?php } ?>

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