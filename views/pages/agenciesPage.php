<?php

/**
 * @file agenciesPage.php
 * Fichier de la page d'administration des agences.
 *
 * Cette page est dédiée à la gestion des agences par un administrateur.
 * Elle présente un formulaire pour ajouter de nouvelles agences et un tableau
 * listant toutes les agences existantes avec une option de suppression.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 *
 * @uses array $agencies Un tableau d'objets ou de tableaux associatifs, chaque élément représentant une agence.
 * @uses generateCSRFToken() Une fonction (non définie ici) pour générer un jeton CSRF, utilisée pour le formulaire d'ajout.
 * @uses htmlspecialchars() Pour échapper les données affichées et prévenir les attaques XSS.
 * @uses count() Pour afficher le nombre d'agences.
 */
?>
<div class="container mt-5 mb-5">
    <div class="row">

        <!-- COLONNE GAUCHE : FORMULAIRE D'AJOUT D'AGENCE -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Nouvelle Agence</h5>
                </div>
                <div class="card-body">
                    <!-- Formulaire pour ajouter une nouvelle agence -->
                    <form action="index.php?page=createAgencyAction" method="POST">
                        <!-- Champ caché pour le jeton CSRF, essentiel pour la sécurité -->
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <div class="mb-3">
                            <label for="ville" class="form-label fw-bold">Nom de la ville</label>
                            <input type="text" class="form-control" id="ville" name="ville" placeholder="Ex: Bordeaux" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- COLONNE DROITE : LISTE DES AGENCES -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-building"></i> Liste des Agences</h5>
                    <!-- Affiche le nombre total d'agences -->
                    <span class="badge bg-secondary"><?= count($agencies) ?> agences</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <!-- Tableau listant toutes les agences enregistrées -->
                        <table class="table table-hover table-striped mb-0 text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Ville</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($agencies)): ?>
                                    <tr>
                                        <td colspan="3" class="text-muted py-4">Aucune agence enregistrée.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($agencies as $agence): ?>
                                        <tr>
                                            <td><?= $agence['Id_Agence'] ?></td>
                                            <td class="fw-bold text-primary"><?= htmlspecialchars($agence['ville']) ?></td>
                                            <td>
                                                <!-- Bouton Supprimer l'agence -->
                                                <a href="index.php?page=deleteAgencyAction&id=<?= $agence['Id_Agence'] ?>"
                                                    class="btn btn-danger"
                                                    title="Supprimer"
                                                    onclick="return confirm('Attention !\nSupprimer cette agence supprimera aussi tous les utilisateurs et trajets liés.\n\nÊtes-vous sûr ?');">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                                    </svg>
                                                </a>
                                                <!-- Bouton Modifier l'agence -->
                                                <a href="index.php?page=editAgencyPage&id=<?= $agence['Id_Agence'] ?>"
                                                    class="btn btn-warning me-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                    </svg>
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
    </div>
</div>