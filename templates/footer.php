<?php

$mainMenu = require __DIR__ . '/../App/Config/menu.php';
use App\Tools\NavigationTools;
?>

</main>
<div class="container">
    <footer class="d-flex flex-column align-items-center py-3 my-4 border-top border-dark border-2">
        <ul class="nav w-100 justify-content-center align-items-center list-unstyled mb-3">
        <?php foreach ($mainMenu as $item) {  ?>
                    <li><a href="/index.php?controller=<?=  $item['controller'] ?>&action=<?=  $item['action'] ?> "class="nav-link px-2 <?= NavigationTools::addActiveClass($item['controller'], $item['action']) ?>"><?= $item['label'] ?></a></li>
                <?php } ?>
        </ul>
        <div class="w-100 d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
            <div class="mb-3 mb-md-0 text-center text-md-start">
                <p class="mb-2"> 🍽️ <strong>Vite & Gourmand</strong> - Traiteur en ligne</p>
                <p class=" mb-1 small">12 rue des Gourmets, 33000 Bordeaux, France</p>
                <p class="mb2 small">
                    <strong>Horaires :</strong><br>
                    Lundi au Samedi : 9h00 - 18h00<br>
                    Dimanche : Fermé
                </p>
            </div>
            <div class="mb-3 mb-md-0 text-center">
                <p class="mb-1 small">Suivez-nous sur les réseaux sociaux :</p>
                <a href="#" class="text-decoration-none me-2">Facebook</a>
                <a href="#" class="text-decoration-none me-2">Instagram</a>
                <a href="#" class="text-decoration-none">Twitter</a>
                <p class="mb-0 small">Contact : vitegourmand.projet@gmail.com</p>
            </div>
            <div>
                <p class="mb-0 text-body-secondary">© <?= date('Y') ?> - Tous droits réservés </p>
                <ul class="nav flex-column align-items-center list-unstyled mb-0">
                    <li><a href="?page=mentions-legales" class="nav-link px-2 link-primary">Mentions légales</a></li>
                    <li><a href="?page=cgv" class="nav-link px-2 link-primary">Conditions générales de vente</a></li>
                    <li><a href="?page=cgu" class="nav-link px-2 link-primary">Conditions générales d’Utilisation</a></li>
                </ul>
            </div>
        </div>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>