<?php

/**
 * @file editUserPage.php
 * Fichier de la page de modification d'un utilisateur.
 *
 * Cette page affiche un formulaire permettant à un administrateur de modifier
 * les informations d'un utilisateur existant. Les champs du formulaire
 * sont pré-remplis avec les données actuelles de l'utilisateur.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 *
 * @uses array $userToEdit Un tableau associatif contenant les informations de l'utilisateur à modifier.
 * @uses array $agencies Un tableau d'objets ou de tableaux associatifs représentant les agences disponibles pour le menu déroulant.
 * @uses htmlspecialchars() Pour échapper les données affichées dans les champs du formulaire et prévenir les attaques XSS.
 */

?>

<!-- Conteneur principal de la page de modification d'utilisateur -->
<div class="container mt-5">
    <div class="card shadow">
        <!-- En-tête de la carte -->
        <div class="card-header bg-warning text-dark">
            <h3 class="mb-0">Modifier <?= htmlspecialchars($user['prenom_utilisateur'] . ' ' . $user['nom_utilisateur']) ?></h3>
        </div>
        <!-- Corps de la carte contenant le formulaire -->
        <div class="card-body">
            <!-- Le formulaire envoie les données à la page "updateUserAction" pour la mise à jour -->
            <form action="index.php?page=updateUserAction" method="POST">

                <!-- Champ caché pour le jeton CSRF, essentiel pour la sécurité -->
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <!-- Champ caché contenant l'ID de l'utilisateur à modifier -->
                <input type="hidden" name="id_user" value="<?= $user['Id_Utilisateur'] ?>">

                <!-- Ligne pour le Nom et le Prénom -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" class="form-control" name="nom" value="<?= escape($user['nom_utilisateur']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" class="form-control" name="prenom" value="<?= escape($user['prenom_utilisateur']) ?>" required>
                    </div>
                </div>

                <!-- Ligne pour l'Email et le Téléphone -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= escape($user['email']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" class="form-control" name="tel" value="<?= escape($user['telephone']) ?>" required>
                    </div>
                </div>

                <!-- Sélection de l'agence : Liste déroulante avec les agences disponibles -->
                <div class="mb-3">
                    <label class="form-label">Agence</label>
                    <select name="id_agence" class="form-select" required>
                        <!-- Boucle pour afficher toutes les agences -->
                        <?php foreach ($agencies as $agence): ?>
                            <option value="<?= $agence['Id_Agence'] ?>" <?= ($agence['Id_Agence'] == $user['Id_Agence']) ? 'selected' : '' ?>>
                                <?= escape($agence['ville']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Case à cocher pour le statut d'administrateur -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="is_admin" id="isAdmin" value="1" <?= ($user['admin'] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="isAdmin">Est Administrateur</label>
                </div>

                <!-- Boutons d'action : Annuler ou Enregistrer les modifications -->
                <div class="d-flex justify-content-between">
                    <a href="index.php?page=usersPage" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>