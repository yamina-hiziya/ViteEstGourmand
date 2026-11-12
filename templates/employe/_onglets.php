<?php
$onglets = [
    'commandes' => 'Commandes',
    'avis' => 'Avis',
];
?>
<div class="d-flex flex-wrap gap-2 mb-4">
    <?php foreach ($onglets as $action => $libelle) { ?>
        <a href="index.php?controller=employe&action=<?= $action ?>" class="btn <?= $action === $ongletActif ? 'btn-primary' : 'btn-outline-primary' ?>"><?= $libelle ?></a>
    <?php } ?>
</div>