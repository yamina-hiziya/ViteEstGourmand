// On récupère le formulaire et la zone des cartes
const formulaire = document.getElementById('filtres-menus');
const listeMenus = document.getElementById('liste-menus');

// Fonction qui envoie les filtres au serveur et affiche le résultat
async function filtrerMenus() {
    // On récupère toutes les valeurs du formulaire
    const donnees = new FormData(formulaire);
    const parametres = new URLSearchParams(donnees);

    // On appelle l'action filter, avec les filtres dans l'URL
    const reponse = await fetch('/index.php?controller=menu&action=filter&' + parametres.toString());
    const html = await reponse.text();

    // On remplace les anciennes cartes par les nouvelles
    listeMenus.innerHTML = html;
}

// À chaque modification d'un champ, on relance le filtre
formulaire.addEventListener('input', filtrerMenus);