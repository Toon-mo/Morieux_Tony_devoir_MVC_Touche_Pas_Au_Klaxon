<?php

/**
 * @file addTrajetPage.php
 * Fichier de la page du formulaire pour proposer un nouveau trajet.
 *
 * Ce fichier contient le formulaire HTML permettant à un utilisateur connecté
 * de créer et publier un nouveau trajet. Les informations du conducteur sont
 * pré-remplies à partir de la session, et les listes d'agences sont chargées
 * dynamiquement. Un token CSRF est inclus pour la sécurité.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 *
 * @uses $_SESSION['user'] Pour afficher les informations de l'utilisateur connecté (conducteur) et les transmettre implicitement.
 * @uses array $agencies Un tableau d'objets ou de tableaux associatifs représentant les agences disponibles pour les choix de départ et d'arrivée.
 * @uses generateCSRFToken() Une fonction (non définie ici) pour générer un jeton CSRF.
 * @uses htmlspecialchars() Pour échapper les données affichées et prévenir les attaques XSS.
 */
?>
<div class="container mt-5 mb-5">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="bi bi-plus-circle"></i> Proposer un nouveau trajet</h3>
        </div>
        <div class="card-body">

            <!-- Affichage des messages d'erreur éventuels -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <div style="color: red; background: #fee; border: 1px solid red; padding: 10px; margin-bottom: 20px;">
                    <?= $_SESSION['error_message']; ?>
                </div>
                <?php unset($_SESSION['error_message']); // Très important pour que le message disparaisse 
                ?>
            <?php endif; ?>

            <!-- Le formulaire pointe vers l'action de CRÉATION -->
            <form action="index.php?page=createTrajetAction" method="POST">
                <!-- Champ caché pour le jeton CSRF, essentiel pour la sécurité -->
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <!-- Infos Conducteur (Pré-remplies depuis la SESSION) -->
                <h5 class="mb-3 text-secondary border-bottom pb-2">
                    <i class="bi bi-person-circle"></i> Mes informations
                </h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Conducteur</label>
                        <input type="text" class="form-control bg-light"
                            value="<?= htmlspecialchars($_SESSION['user']['userFirstname'] . ' ' . $_SESSION['user']['userLastname']) ?>" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" class="form-control bg-light"
                            value="<?= htmlspecialchars($_SESSION['user']['telephone']) ?>" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control bg-light"
                            value="<?= htmlspecialchars($_SESSION['user']['email']) ?>" disabled>
                    </div>
                </div>

                <!-- Détails du Trajet -->
                <h5 class="mb-3 text-secondary border-bottom pb-2 mt-4">
                    <i class="bi bi-geo-alt"></i> Détails du voyage
                </h5>

                <div class="row">
                    <!-- Agence de Départ -->
                    <div class="col-md-6 mb-3">
                        <label for="agence_depart" class="form-label fw-bold">
                            <i class="bi bi-arrow-up-circle"></i> Agence de départ
                        </label>
                        <select name="agence_depart" id="agence_depart" class="form-select" required>
                            <option value="" selected disabled>Choisir une ville...</option>
                            <?php foreach ($agencies as $agence): ?>
                                <option value="<?= $agence['Id_Agence'] ?>">
                                    <?= htmlspecialchars($agence['ville']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Agence d'Arrivée -->
                    <div class="col-md-6 mb-3">
                        <label for="agence_arrivee" class="form-label fw-bold">
                            <i class="bi bi-arrow-down-circle"></i> Agence d'arrivée
                        </label>
                        <select name="agence_arrivee" id="agence_arrivee" class="form-select" required>
                            <option value="" selected disabled>Choisir une ville...</option>
                            <?php foreach ($agencies as $agence): ?>
                                <option value="<?= $agence['Id_Agence'] ?>">
                                    <?= htmlspecialchars($agence['ville']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- Date et Heure de Départ -->
                    <div class="col-md-6 mb-3">
                        <label for="date_depart" class="form-label fw-bold">
                            <i class="bi bi-calendar-event"></i> Date et Heure de départ
                        </label>
                        <input type="datetime-local" name="date_depart" id="date_depart" class="form-control" required>
                        <small class="form-text text-muted">Le départ doit être dans le futur</small>
                    </div>

                    <!-- Date et Heure d'Arrivée -->
                    <div class="col-md-6 mb-3">
                        <label for="date_arrivee" class="form-label fw-bold">
                            <i class="bi bi-calendar-check"></i> Date et Heure d'arrivée
                        </label>
                        <input type="datetime-local" name="date_arrivee" id="date_arrivee" class="form-control" required>
                        <small class="form-text text-muted">Minimum 60 minutes après le départ</small>
                    </div>
                </div>

                <!-- Nombre de places proposées -->
                <!-- 8 places maximum pour un permis B avec minivan -->
                <div class="mb-4">
                    <label for="nb_places" class="form-label fw-bold">
                        <i class="bi bi-people"></i> Nombre total de places proposées
                    </label>
                    <input type="number" name="nb_places" id="nb_places" class="form-control"
                        min="1" max="8" placeholder="Ex: 3" required>
                    <small class="form-text text-muted">Entre 1 et 8 places disponibles</small>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php?page=connected" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-check-circle"></i> Publier le trajet
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>