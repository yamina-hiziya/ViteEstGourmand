# Vite & Gourmand

Application web d'un traiteur bordelais : les visiteurs consultent et filtrent les menus, les clients les commandent en ligne, les employés gèrent les commandes et le catalogue, et l'administrateur suit les statistiques.

Projet réalisé dans le cadre du titre professionnel Développeur Web et Web Mobile (Studi).

- Application en ligne : https://yamina.alwaysdata.net
- Gestion de projet (Trello) : https://trello.com/b/e6LKRG4R/vite-gourmand-rattrapage-back-end

## Technologies

| Partie | Technologie |
|---|---|
| Serveur | PHP 8.2, architecture MVC sans framework |
| Base relationnelle | MySQL 8 (MariaDB en production) |
| Base non relationnelle | MongoDB 7 (statistiques) |
| Interface | HTML, CSS, Bootstrap 5, JavaScript (fetch), Chart.js |
| Mails | PHPMailer (Mailpit en local) |
| Tests | PHPUnit 11 |
| Environnement | Docker et Docker Compose |

## Prérequis

- Docker Desktop (avec Docker Compose)
- Git

PHP, MySQL, MongoDB et Composer n'ont pas besoin d'être installés sur l'ordinateur : ils tournent dans les conteneurs Docker.

## Installation en local

### 1. Récupérer le projet

```bash
git clone https://github.com/yamina-hiziya/ViteEstGourmand.git
cd ViteEstGourmand
```

### 2. Créer les fichiers de configuration

Les fichiers de configuration contiennent des identifiants : ils ne sont pas publiés sur GitHub. On les crée à partir des modèles fournis, déjà remplis pour Docker :

```bash
cp App/Config/db_config.example.php App/Config/db_config.php
cp App/Config/mail_config.example.php App/Config/mail_config.php
cp App/Config/mongo_config.example.php App/Config/mongo_config.php
```

### 3. Lancer les conteneurs

```bash
docker compose up -d --build
```

Cinq services démarrent :

| Service | Rôle | Adresse |
|---|---|---|
| web | PHP 8.2 et Apache | http://localhost:8095 |
| db | MySQL 8 | port 3315 |
| phpmyadmin | Gestion de la base | http://localhost:8096 |
| mongo | MongoDB 7 | port 27019 |
| mailpit | Boîte de réception des mails de test | http://localhost:8025 |

### 4. Installer les dépendances PHP

```bash
docker compose exec web composer install
```

### 5. Importer la base de données

1. Ouvrir phpMyAdmin : http://localhost:8096 (utilisateur `root`, mot de passe `root`).
2. Sélectionner la base `vite_gourmand`.
3. Onglet **Importer** : importer `sql/vite_gourmand_creation.sql` (les tables).
4. Onglet **Importer** : importer `sql/vite_gourmand_donnees.sql` (les données de démonstration).

### 6. Ouvrir l'application

http://localhost:8095

Les identifiants des comptes de test (administrateur, employé, client) sont donnés dans le manuel d'utilisation.

### 7. Remplir les statistiques MongoDB

Se connecter en administrateur, ouvrir **Administration › Statistiques** et cliquer sur **Synchroniser avec MongoDB** : les commandes de MySQL sont recopiées dans MongoDB.

## Lancer les tests

```bash
docker compose exec web vendor/bin/phpunit
```

## Organisation du code

```
index.php            Point d'entrée : toutes les requêtes passent par ce fichier
App/Config/          Configuration (les fichiers *.example.php sont les modèles)
App/Controller/      Contrôleurs : droits, contrôles, appel des repositories, affichage
App/Entity/          Entités : une ligne de table, règles métier (prix, livraison)
App/Repository/      Requêtes SQL (PDO) et MongoDB
App/Tools/           Outils partagés : Mailer, Csrf, GeoTools
templates/           Vues HTML
public/assets/       CSS, JavaScript et images
tests/               Tests unitaires PHPUnit
sql/                 Création de la base et données de démonstration
docs/                MCD, MLD, diagrammes de cas d'utilisation et de séquence
```

## Branches Git

- `main` : version stable, déployée en ligne
- `develop` : branche de développement
- `feature/...` : une branche par fonctionnalité, fusionnée dans `develop` après test

## Déploiement

L'application est hébergée sur AlwaysData (PHP 8.2, MariaDB), avec MongoDB Atlas pour les statistiques et le serveur SMTP de Gmail pour les mails. Les étapes du déploiement sont détaillées dans la documentation technique.