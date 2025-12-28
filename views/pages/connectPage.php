<?php

/**
 * @file connectedPage.php
 * @brief Vue pour la page des trajets proposés par l'utilisateur connecté.
 * Affiche les messages d'erreur et de succés, ainsi que le contenu principal des trajets
 * proposés.
 * Cette vue est incluse dans le fichier de mise en page principal (`layout.php`)
 * et utilise un autre fichier de vue (`mainContent.php`) pour afficher le contenu
 * principal (généralement le tableau des trajets).
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 */
?>

<div class="container mt-4">
    <!-- Affichage des messages d'erreur -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Erreur :</strong> <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Affichage des messages de succès -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <strong>Succès :</strong> <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <h1 class="text mb-4">Mon Espace - Trajets Proposés</h1>

    <!-- Inclusion du contenu principal de la page (affichage des trajets) -->
    <?php require_once(ROOT . "/views/components/mainContent.php"); ?>
</div>

<!-- Inclusion du contenu principal de la page (affichage des trajets) -->
<?php
require_once(ROOT . "/views/components/mainContent.php");
?>