<?php

/**
 * @file adminTrajetPage.php
 * Fichier de la page d'administration des trajets.
 *
 * Ce fichier affiche un tableau récapitulatif de tous les trajets actifs,
 * y compris les informations sur le conducteur, l'itinéraire, les horaires,
 * et le nombre de places. Il permet aux administrateurs de visualiser et
 * de supprimer des trajets.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 *
 * @uses array $trajets Un tableau d'objets ou de tableaux associatifs, chaque élément représentant un trajet
 *                      avec des détails sur le conducteur (prénom, nom, email) et les agences (ID).
 * @uses array $agencies_by_id Un tableau associatif mappant les IDs d'agence à leurs noms de ville,
 *                              utilisé pour afficher les noms des agences de départ et d'arrivée.
 * @uses date() Pour formater les dates et heures d'affichage.
 * @uses htmlspecialchars() Pour échapper les données affichées et prévenir les attaques XSS.
 */
?>
<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="bi bi-car-front-fill"></i> Administration des Trajets</h3>
            <!-- Affiche le nombre total de trajets actifs -->
            <span class="badge bg-light text-dark"><?= count($trajets) ?> trajets actifs</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Conducteur</th>
                            <th>Itinéraire</th>
                            <th>Horaires</th>
                            <th>Places</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($trajets)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <em>Aucun trajet planifié pour le moment.</em>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($trajets as $trajet): ?>
                                <tr>
                                    <!-- ID Technique du trajet -->
                                    <td class="text-muted small">#<?= $trajet['Id_Trajet'] ?></td>

                                    <!-- Informations sur le conducteur -->
                                    <td class="text-start">
                                        <div class="fw-bold">
                                            <?= htmlspecialchars($trajet['prenom_utilisateur'] . ' ' . $trajet['nom_utilisateur']) ?>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="bi bi-envelope"></i> <?= htmlspecialchars($trajet['email']) ?>
                                        </div>
                                    </td>

                                    <!-- Itinéraire (agences de départ et d'arrivée) -->
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($agencies_by_id[$trajet['Id_Agence_Depart']] ?? '?') ?>
                                        </span>
                                        <i class="bi bi-arrow-right-short text-muted"></i>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($agencies_by_id[$trajet['Id_Agence_Arrivee']] ?? '?') ?>
                                        </span>
                                    </td>

                                    <!-- Horaires du trajet -->
                                    <td class="small">
                                        <div class="fw-bold text-primary">
                                            <?= date('d/m/Y', strtotime($trajet['date_heure_depart'])) ?>
                                        </div>
                                        <div>
                                            <?= date('H:i', strtotime($trajet['date_heure_depart'])) ?>
                                            à
                                            <?= date('H:i', strtotime($trajet['date_heure_arrivee'])) ?>
                                        </div>
                                    </td>

                                    <!-- Nombre de places disponibles et total -->
                                    <td>
                                        <span class="badge <?= $trajet['nb_places_dispo'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $trajet['nb_places_dispo'] ?> / <?= $trajet['nb_places_total'] ?>
                                        </span>
                                    </td>

                                    <!-- Actions d'administration -->
                                    <td>
                                        <!-- Bouton Supprimer le trajet -->
                                        <a href="index.php?page=deleteTrajet&id=<?= $trajet['Id_Trajet'] ?>"
                                            class="btn btn-danger"
                                            title="Supprimer ce trajet définitivement"
                                            onclick="return confirm('ADMINISTRATION :\nÊtes-vous certain de vouloir supprimer ce trajet ?\nCette action est irréversible.');">
                                            <i class="bi bi-trash3"></i> Supprimer
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>