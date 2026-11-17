<?php require_once _ROOTPATH_ . '/templates/header.php';
use App\Entity\Commande;
/** @var array $commande */
/** @var array $lignes */
/** @var array $suivi */
$total = $commande['prix_menu'] + $commande['prix_livraison'];
$modifiable = !in_array($commande['statut'], ['terminée', 'annulée'], true);
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <a href="index.php?controller=employe&action=commandes" class="d-inline-block mb-3">← Toutes les commandes</a>
            <h1 class="mb-1">Commande <?= htmlspecialchars($commande['numero_commande']) ?></h1>
            <p class="text-muted mb-4"><?= htmlspecialchars($commande['menu_titre']) ?> — statut actuel : <strong><?= htmlspecialchars($commande['statut']) ?></strong></p>

            <?php if (!empty($succes)) { ?>
                <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
            <?php } ?>
            <?php if (!empty($erreur)) { ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
            <?php } ?>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">Client</h2>
                            <ul class="list-unstyled mb-0">
                                <li><?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']) ?></li>
                                <li><a href="mailto:<?= htmlspecialchars($commande['email']) ?>"><?= htmlspecialchars($commande['email']) ?></a></li>
                                <li><a href="tel:<?= htmlspecialchars($commande['telephone']) ?>"><?= htmlspecialchars($commande['telephone']) ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">Prestation</h2>
                            <ul class="list-unstyled mb-0">
                                <li><?= date('d/m/Y', strtotime($commande['date_prestation'])) ?> à <?= htmlspecialchars(substr($commande['heure_livraison'], 0, 5)) ?></li>
                                <li><?= htmlspecialchars($commande['adresse_livraison']) ?>, <?= htmlspecialchars($commande['ville_livraison']) ?></li>
                                <li><?= $commande['nombre_personne'] ?> personnes — <?= number_format($total, 2, ',', ' ') ?> €</li>
                                <li>Matériel prêté : <?= $commande['pret_materiel'] ? 'oui' : 'non' ?><?= $commande['restitution_materiel'] ? ' (restitué)' : '' ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5">Plats</h2>
                    <ul class="mb-0">
                        <?php foreach ($lignes as $ligne) { ?>
                            <li><?= htmlspecialchars(ucfirst($ligne['type_plat'])) ?> : <?= htmlspecialchars($ligne['titre_plat']) ?> × <?= $ligne['quantite'] ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5">Historique</h2>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($suivi as $etape) { ?>
                            <li class="list-group-item px-0">
                                <strong><?= htmlspecialchars(ucfirst($etape['statut'])) ?></strong>
                                <span class="text-muted"> — le <?= date('d/m/Y à H:i', strtotime($etape['date_heure'])) ?></span>
                                <?php if ($etape['motif']) { ?>
                                    <div class="small text-muted">
                                        <?= htmlspecialchars($etape['motif']) ?>
                                        <?= $etape['mode_contact'] ? '(contact par ' . htmlspecialchars($etape['mode_contact']) . ')' : '' ?>
                                    </div>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <?php if ($modifiable) { ?>
                <div class="card">
                    <div class="card-body">
                        <h2 class="h5">Changer le statut</h2>
                        <form method="POST" action="index.php?controller=employe&action=statut">
                        <?= \App\Tools\Csrf::champ() ?>
                            <input type="hidden" name="numero_commande" value="<?= htmlspecialchars($commande['numero_commande']) ?>">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="statut" class="form-label">Nouveau statut</label>
                                    <select id="statut" name="statut" class="form-select" required>
                                        <?php foreach (Commande::STATUTS as $s) {
                                            if ($s === $commande['statut']) {
                                                continue;
                                            }
                                        ?>
                                            <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars(ucfirst($s)) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="mode_contact" class="form-label">Mode de contact</label>
                                    <select id="mode_contact" name="mode_contact" class="form-select">
                                        <option value="">—</option>
                                        <option value="téléphone">Téléphone</option>
                                        <option value="email">Email</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="motif" class="form-label">Motif</label>
                                    <input type="text" id="motif" name="motif" class="form-control">
                                </div>
                            </div>
                            <div class="form-text mb-3">Le mode de contact et le motif sont obligatoires pour une annulation.</div>
                            <button type="submit" name="changerStatut" class="btn btn-primary">Enregistrer</button>
                        </form>
                    </div>
                </div>
            <?php } else { ?>
                <div class="alert alert-secondary">Cette commande est <?= htmlspecialchars($commande['statut']) ?> : son statut ne peut plus être modifié.</div>
            <?php } ?>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>