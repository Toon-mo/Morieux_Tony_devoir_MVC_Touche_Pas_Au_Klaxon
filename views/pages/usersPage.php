<?php

/**
 * @file usersPage.php
 * Fichier de la page de gestion des utilisateurs (réservée aux administrateurs).
 *
 * Cette page permet aux administrateurs de visualiser la liste des collaborateurs,
 * d'en ajouter de nouveaux via un formulaire, et de modifier ou supprimer
 * les comptes existants.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 *
 * @uses array $users Un tableau d'objets ou de tableaux associatifs représentant tous les utilisateurs.
 * @uses array $agencies_by_id Un tableau associatif (Id_Agence => ville) pour l'affichage du nom de l'agence.
 * @uses getAgencies() Une fonction pour récupérer la liste de toutes les agences pour le formulaire d'ajout.
 * @uses generateCSRFToken() Pour générer un jeton de sécurité pour le formulaire d'ajout.
 * @uses escape() Une fonction d'échappement pour prévenir les failles XSS lors de l'affichage des données.
 * @uses $_SESSION['user']['id'] Pour empêcher la suppression de l'utilisateur actuellement connecté.
 */

use Morieuxtony\MvcTest\Models\AgenceModel;

?>

<div class="container mt-5 mb-5">
    <div class="row">

        <!-- Colonne pour le FORMULAIRE D'AJOUT d'utilisateur (Gauche) -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5><i class="bi bi-person-plus"></i> Ajouter</h5>
                </div>
                <div class="card-body">
                    <!-- Formulaire pour la création d'un nouvel utilisateur -->
                    <form action="index.php?page=createUserAction" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <div class="mb-2"><input type="text" class="form-control form-control-sm" name="nom" placeholder="Nom" required></div>
                        <div class="mb-2"><input type="text" class="form-control form-control-sm" name="prenom" placeholder="Prénom" required></div>
                        <div class="mb-2"><input type="email" class="form-control form-control-sm" name="email" placeholder="Email" required></div>
                        <div class="mb-2"><input type="text" class="form-control form-control-sm" name="tel" placeholder="Téléphone" required></div>

                        <!-- Liste déroulante pour la sélection de l'agence -->
                        <div class="mb-2">
                            <select name="id_agence" class="form-select form-select-sm" required>
                                <option value="" selected disabled>Choisir agence...</option>
                                <?php $agencies = AgenceModel::getAgencies(); // Récupère toutes les agences
                                foreach ($agencies as $a): ?>
                                    <option value="<?= $a['Id_Agence'] ?>"><?= $a['ville'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Champ pour le mot de passe (valeur par défaut en lecture seule) -->
                        <div class="mb-2"><input type="password" class="form-control form-control-sm bg-light" name="mdp" value="password123" readonly title="Mot de passe par défaut"></div>

                        <!-- Case à cocher pour le statut d'administrateur -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="is_admin" id="chkAdmin" value="1">
                            <label class="form-check-label small" for="chkAdmin">Admin ?</label>
                        </div>

                        <!-- Bouton de soumission -->
                        <button type="submit" class="btn btn-primary btn-sm w-100">Créer</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Colonne pour la LISTE des collaborateurs (Droite) -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5>Liste des collaborateurs</h5>
                </div>
                <div class="card-body p-0">
                    <!-- Tableau affichant la liste des utilisateurs -->
                    <table class="table table-striped mb-0 text-center small align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Agence</th>
                                <th>Rôle</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Boucle sur chaque utilisateur -->
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td class="text-start">
                                        <strong><?= escape($u['nom_utilisateur'] . ' ' . $u['prenom_utilisateur']) ?></strong><br>
                                        <span class="text-muted"><?= escape($u['email']) ?></span>
                                    </td>
                                    <td><?= $agencies_by_id[escape($u['Id_Agence'])] ?? '-' ?></td>
                                    <!-- Affichage du rôle (Admin ou Employé) -->
                                    <td><?= ($u['admin'] == 1) ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-secondary">Employé</span>' ?></td>
                                    <td>
                                        <!-- BOUTON MODIFIER -->
                                        <a href="index.php?page=editUserPage&id=<?= $u['Id_Utilisateur'] ?>" class="btn btn-warning btn-sm py-0">
                                            <!-- Icône crayon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                            </svg>
                                        </a>

                                        <!-- BOUTON SUPPRIMER : Affiché uniquement si ce n'est pas l'utilisateur connecté -->
                                        <?php if ($_SESSION['user']['id'] != $u['Id_Utilisateur']): ?>
                                            <a href="index.php?page=deleteUserAction&id=<?= $u['Id_Utilisateur'] ?>" class="btn btn-danger btn-sm py-0" onclick="return confirm('Confirmez-vous la suppression de l\'utilisateur ? Cette action est irréversible.')">
                                                <!-- Icône poubelle -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                                    <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>