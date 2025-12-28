<?php

/**
 * @file editTrajetPage.php
 * Fichier de la page d'édition d'un trajet existant.
 *
 * Cette page contient le formulaire permettant à un utilisateur connecté
 * de modifier les informations d'un trajet qu'il a précédemment créé.
 * Les champs du formulaire sont pré-remplis avec les données actuelles du trajet.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 *
 * @uses $_SESSION['user'] Pour vérifier si un utilisateur est connecté.
 * @uses array $trajet Un tableau associatif contenant les informations du trajet à modifier.
 * @uses array $agencies Un tableau d'objets ou de tableaux associatifs représentant les agences disponibles pour les choix de départ et d'arrivée.
 * @uses htmlspecialchars() Pour échapper les données affichées et prévenir les attaques XSS.
 * @uses date() Pour formater les dates et heures pour l'affichage dans les champs datetime-local.
 */

// Récupère les infos utilisateur depuis la session
$user = $_SESSION['user'] ?? null;

// Vérifie si un utilisateur est connecté
$isConnected = ($user && isset($user['userLastname']));
?>

<!--  Page d'édition de trajet -->

<!-- Affichage du formulaire d'édition avec les données pré-remplies -->

<div class="container mt-5">
    <h2 class="mb-4">Modifier mon trajet</h2>

    <!-- Le formulaire envoie les données à la page "updateTrajetAction" pour la mise à jour -->
    <form action="index.php?page=updateTrajetAction" method="POST" class="w-50 mx-auto bg-light p-4 rounded shadow">

        <!-- Champ caché contenant l'ID du trajet à modifier -->
        <?php
        // Affiche le jeton qui sera placé dans le formulaire
        $generatedToken = generateCSRFToken(); // Appelle la fonction generateCSRFToken() ici
        echo "<!-- DEBUG: Jeton généré dans la vue: " . htmlspecialchars($generatedToken) . " -->";
        ?>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($generatedToken) ?>">
        <input type="hidden" name="Id_Trajet" value="<?= $trajet['Id_Trajet'] ?>">

        <!-- Agence de Départ : Liste déroulante avec les agences disponibles -->
        <div class="mb-3">
            <label class="form-label">Agence de départ</label>
            <select name="agence_depart" class="form-select" required>
                <!-- Boucle pour afficher toutes les agences -->
                <?php foreach ($agencies as $agence): ?>
                    <option value="<?= $agence['Id_Agence'] ?>"
                        <?= ($trajet['Id_Agence_Depart'] == $agence['Id_Agence']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($agence['ville']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Date et Heure de Départ : Champ datetime-local pré-rempli avec la valeur actuelle -->
        <div class="mb-3">
            <label class="form-label">Date et Heure de départ</label>
            <input type="datetime-local" name="date_depart" class="form-control"
                value="<?= date('Y-m-d\TH:i', strtotime($trajet['date_heure_depart'])) ?>" required>
        </div>

        <!-- Agence d'Arrivée : Liste déroulante avec les agences disponibles -->
        <div class="mb-3">
            <label class="form-label">Agence d'arrivée</label>
            <select name="agence_arrivee" class="form-select" required>
                <!-- Boucle pour afficher toutes les agences -->
                <?php foreach ($agencies as $agence): ?>
                    <option value="<?= $agence['Id_Agence'] ?>"
                        <?= ($trajet['Id_Agence_Arrivee'] == $agence['Id_Agence']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($agence['ville']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Date et Heure d'Arrivée : Champ datetime-local pré-rempli avec la valeur actuelle -->
        <div class="mb-3">
            <label class="form-label">Date et Heure d'arrivée</label>
            <input type="datetime-local" name="date_arrivee" class="form-control"
                value="<?= date('Y-m-d\TH:i', strtotime($trajet['date_heure_arrivee'])) ?>" required>
        </div>

        <!-- Nombre total de places : Champ numérique pré-rempli avec la valeur actuelle -->
        <div class="mb-3">
            <label class="form-label">Nombre de places total</label>
            <input type="number" name="nb_places" class="form-control" min="1"
                value="<?= $trajet['nb_places_total'] ?>" required>
        </div>

        <!-- Boutons d'action : Annuler ou Enregistrer les modifications -->
        <div class="d-flex justify-content-between mt-4">
            <a href="index.php?page=connected" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
        </div>
    </form>

</div>