<?php

/**
 * @file loginPage.php
 * Fichier de la page de connexion des utilisateurs.
 *
 * Cette page présente le formulaire de connexion où les utilisateurs peuvent
 * saisir leur email et leur mot de passe pour accéder à leur compte.
 * Elle gère également l'affichage des messages d'erreur, comme les
 * identifiants incorrects, et inclut une protection contre les attaques CSRF.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 *
 * @uses $_GET['error'] Pour afficher un message d'erreur en cas d'échec de la connexion.
 * @uses generateCSRFToken() Pour générer un jeton de sécurité et le placer dans un champ caché du formulaire.
 */

?>
<?php
/**
 * Génère un jeton CSRF pour sécuriser le formulaire de connexion.
 * @return string le jeton CSRF généré.
 */
require_once(ROOT . "/config/security.php");
?>

<!-- Conteneur principal centré pour le formulaire de connexion -->
<div class="container d-flex justify-content-center mt-5">
    <div class="card shadow" style="width: 22rem;">
        <!-- En-tête de la carte -->
        <div class="card-header bg-dark text-white text-center">
            <h5 class="card-title mb-0">Connexion</h5>
        </div>
        <div class="card-body">

            <!-- Affichage d'un message d'erreur si les identifiants sont incorrects -->
            <?php if (isset($_GET['error']) && $_GET['error'] === 'bad_credentials'): ?>
                <div class="alert alert-danger text-center small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> Email ou mot de passe incorrect.
                </div>
            <?php endif; ?>

            <!-- Formulaire de connexion -->
            <form method="POST" action="index.php?page=login">
                <!-- Champ caché pour la protection CSRF -->
                <input type="hidden" name="csrf_token" value="<?= \generateCSRFToken() ?>">

                <!-- Champ pour l'email de l'utilisateur -->
                <div class="mb-3">
                    <label for="username" class="form-label">Email utilisateur :</label>
                    <input type="email" class="form-control" id="username" name="username" required autocomplete="username">
                </div>

                <!-- Champ pour le mot de passe -->
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe :</label>
                    <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                </div>

                <!-- Bouton de soumission du formulaire -->
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit">Se connecter</button>
                </div>
            </form>

        </div>
        <!-- Pied de page de la carte -->
        <div class="card-footer text-muted text-center small">
            Pas encore de compte ? Contactez l'administrateur.
        </div>
    </div>
</div>