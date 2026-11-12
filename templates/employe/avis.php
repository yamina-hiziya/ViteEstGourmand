<?php require_once _ROOTPATH_ . '/templates/header.php';
/** @var array $avisListe */
?>

<div class="container my-5">
    <?php $ongletActif = 'avis'; require _ROOTPATH_ . '/templates/employe/_onglets.php'; ?>

    <h1 class="mb-4">Avis à valider</h1>

    <?php if (!empty($succes)) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php } ?>

    <?php if (empty($avisListe)) { ?>
        <p>Aucun avis en attente de validation.</p>
    <?php } ?>

    <div class="row g-4">
        <?php foreach ($avisListe as $avis) { ?>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <p class="mb-1 text-warning"><?= str_repeat('★', $avis['note']) . str_repeat('☆', 5 - $avis['note']) ?></p>
                        <p class="small text-muted mb-2"><?= htmlspecialchars($avis['prenom']) ?> — <?= htmlspecialchars($avis['menu_titre']) ?></p>
                        <p><?= nl2br(htmlspecialchars($avis['description'])) ?></p>
                        <form method="POST" action="index.php?controller=employe&action=moderer" class="d-flex gap-2 mt-auto">
                            <input type="hidden" name="avis_id" value="<?= $avis['avis_id'] ?>">
                            <button type="submit" name="decision" value="validé" class="btn btn-success">Valider</button>
                            <button type="submit" name="decision" value="refusé" class="btn btn-outline-danger">Refuser</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>