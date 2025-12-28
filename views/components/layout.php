<?php

/**
 * @file layout.php
 * Fichier du layout principal de l'application.
 *
 * Ce fichier définit la structure HTML de base de toutes les pages du site.
 * Il inclut la balise `<html>`, le `<head>` avec les métadonnées et les liens CSS,
 * et le `<body>` qui intègre l'en-tête, le contenu spécifique de la page, et le pied de page.
 * Les variables `$title`, `$description` et `$content` sont injectées dynamiquement
 * pour personnaliser chaque page.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Métadonnées de description de la page, injectée dynamiquement -->
    <meta name="description" content="<?= $description ?>">
    <!-- Liens Bootstrap CSS et fichier CSS personnalisé -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="scss/style.css">

    <!-- Titre de la page, injecté dynamiquement -->
    <title><?= $title ?></title>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Inclusion de l'en-tête partagé pour toutes les pages -->
    <?php require_once 'header.php'; ?>

    <!-- Contenu principal de la page, injecté dynamiquement -->
    <main class="container flex-grow-1">
        <?= $content ?>
    </main>

    <!-- Inclusion du pied de page partagé pour toutes les pages -->
    <?php require_once 'footer.php'; ?>

    <!-- Scripts Bootstrap JavaScript pour les fonctionnalités interactives -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>

</html>