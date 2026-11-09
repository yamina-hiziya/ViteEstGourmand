// Pour chaque bouton "œil" de la page
document.querySelectorAll('[data-cible]').forEach(function (bouton) {
    bouton.addEventListener('click', function () {
        // On retrouve le champ grâce à son id
        const champ = document.getElementById(bouton.dataset.cible);
        const estCache = champ.type === 'password';

        // On bascule entre caché et visible
        champ.type = estCache ? 'text' : 'password';

        // On met à jour les informations pour les lecteurs d'écran
        bouton.setAttribute('aria-label', estCache ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        bouton.setAttribute('aria-pressed', estCache ? 'true' : 'false');
    });
});