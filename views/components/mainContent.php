<?php

/**
 * @file maintContent.php
 * 
 * Fichier partiel (template) affichant un tableau récapitulatif des trajets disponibles.
 * Ce template est utilisé pour afficher une liste de trajets avec des détails de départ, d'arrivée,
 * le nombre de places disponibles, et des actions conditionnelles basées sur l'état de connexion
 * de l'utilisateur et ses droits (connecté, administrateur, auteur du trajet).
 * Il intègre des modales pour afficher des informations détaillées sur le conducteur d'un trajet.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 *
 * @uses $_SESSION['user'] Pour récupérer les informations de l'utilisateur connecté, son statut admin et son ID.
 * @uses array $trajets Un tableau d'objets ou de tableaux associatifs, chaque élément représentant un trajet.
 * @uses array $agencies_by_id Un tableau associatif mappant les IDs d'agence à leurs noms de ville, utilisé pour l'affichage.
 * @uses function escape() Une fonction utilitaire (non définie ici) pour échapper les sorties HTML.
 */

//Récupère les informations de l'utilisateur à partir de la session
$user = $_SESSION['user'] ?? null;
// Vérifie si un utilisateur est connecté en s'assurant que la variable de session 'user' existe, a un nom et n'est pas "null".
$isConnected = ($user && isset($user['userLastname']) && $user['userLastname'] !== "null");
?>


<table class="table table-bordered rounded-3 shadow-lg">
    <thead>
        <tr class="table-dark text-center">
            <th scope="col">Départ</th>
            <th scope="col">Date</th>
            <th scope="col">Heure</th>
            <th scope="col">Destination</th>
            <th scope="col">Date</th>
            <th scope="col">Heure</th>
            <th scope="col">Places</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>

    <tbody class="text-center">
        <!-- Boucle à travers chaque trajet pour afficher ses détails dans une ligne de tableau -->
        <?php if (empty($trajets)): ?>
            <tr>
                <!-- Si aucun trajet n'est disponible, affiche un message informatif -->
                <td colspan="8" class="text-muted py-4">
                    <em>Aucun trajet disponible pour le moment.</em>
                </td>
            </tr>

        <?php else: ?>
            <?php foreach ($trajets as $trajet): ?>
                <tr>
                    <!-- Affichage des informations du trajet (Départ, Date, Arrivée...) -->
                    <td class="fw-bold">
                        <?php
                        $id_agence_depart = $trajet['Id_Agence_Depart'];
                        // Affiche le nom de la ville de départ ou 'Ville inconnue' si non trouvée
                        echo htmlspecialchars($agencies_by_id[$id_agence_depart] ?? 'Ville inconnue');
                        ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($trajet['date_heure_depart'])) ?></td>
                    <td><?= date('H:i', strtotime($trajet['date_heure_depart'])) ?></td>
                    <td class="fw-bold">
                        <?php
                        $id_agence_arrivee = $trajet['Id_Agence_Arrivee'];
                        // Affiche le nom de la ville d'arrivée ou 'Ville inconnue' si non trouvée
                        echo htmlspecialchars($agencies_by_id[$id_agence_arrivee] ?? 'Ville inconnue');
                        ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($trajet['date_heure_arrivee'])) ?></td>
                    <td><?= date('H:i', strtotime($trajet['date_heure_arrivee'])) ?></td>
                    <td>
                        <span class="badge bg-success">
                            <?= (int)$trajet['nb_places_dispo'] ?>
                            <?= $trajet['nb_places_dispo'] > 1 ? 'places' : 'place' ?>
                        </span>
                    </td>

                    <!-- COLONNE ACTIONS -->
                    <td>
                        <?php if ($isConnected): ?>

                            <?php
                            // Vérifie si l'utilisateur connecté est l'auteur du trajet
                            $isAuteur = ($user['id'] == $trajet['Id_Conducteur']);
                            // Vérifie si l'utilisateur connecté est un administrateur
                            $isAdmin = (isset($user['admin']) && $user['admin'] == 1);
                            ?>

                            <!-- Bouton "Œil" (pour voir les détails) - Visible pour tous les utilisateurs connectés -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal<?= $trajet['Id_Trajet'] ?>" title="Voir les détails">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                </svg>
                            </button>

                            <!-- Bouton MODIFIER - Visible uniquement pour l'auteur du trajet -->
                            <?php if ($isAuteur): ?>
                                <a href="index.php?page=editTrajetPage&id=<?= $trajet['Id_Trajet'] ?>" class="btn btn-warning btn-sm ms-1" title="Modifier mon trajet">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <!-- Bouton SUPPRIMER - Visible pour l'auteur du trajet ou un administrateur -->
                            <?php if ($isAuteur || $isAdmin): ?>
                                <a href="index.php?page=deleteTrajet&id=<?= $trajet['Id_Trajet'] ?>"
                                    class="btn btn-danger ms-1"
                                    title="Supprimer"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce trajet ?');">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <!-- La Modale pour les détails du trajet et du conducteur -->
                            <div class="modal fade" id="modal<?= $trajet['Id_Trajet'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Détails du trajet</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p><strong>Conducteur :</strong> <?= htmlspecialchars($trajet['prenom_utilisateur'] ?? '') ?> <?= htmlspecialchars($trajet['nom_utilisateur'] ?? '') ?></p>
                                            <p><strong>Téléphone :</strong> <a href="tel:<?= htmlspecialchars($trajet['telephone'] ?? '') ?>"><?= htmlspecialchars($trajet['telephone'] ?? '') ?></a></p>
                                            <p><strong>Email :</strong> <a href="mailto:<?= htmlspecialchars($trajet['email'] ?? '') ?>"><?= htmlspecialchars($trajet['email'] ?? '') ?></a></p>
                                            <hr>
                                            <p class="d-flex justify-content-between">
                                                <strong>Places totales :</strong>
                                                <span class="badge bg-success"><?= $trajet['nb_places_total'] ?></span>
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>

                            <!-- CAS NON CONNECTÉ : Bouton désactivé pour voir les détails -->
                            <button type="button" class="btn btn-secondary btn-sm" disabled title="Connectez-vous pour voir les détails">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
                                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-5.034-5.034c-.717.166-1.398.435-2.008.748C3.12 5.668 1.88 7 0 8s3 5.5 8 5.5c1.614 0 3.1-.563 4.233-1.518l.77.77a7.6 7.6 0 0 1-5.003 1.748C3 13.5 0 8 0 8s3-5.5 8-5.5c.343 0 .679.025 1.01.074zm-2.906-.25a13 13 0 0 1 1.66-2.043l.865.865c-.477.394-.914.834-1.302 1.328v-.15zM1.173 8a13 13 0 0 1 1.66-2.043l1.166 1.166A12 12 0 0 0 1.173 8m4.93 1.57a13 13 0 0 1 .63-.78L5.356 7.39A2.5 2.5 0 0 0 4.5 8a3.5 3.5 0 0 0 1.603 2.77" />
                                    <path d="M1 1 15 15" />
                                </svg>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>