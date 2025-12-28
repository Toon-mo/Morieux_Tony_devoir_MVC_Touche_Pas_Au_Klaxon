<?php

/**
 * @file adminPage.php
 * "Hub" central du tableau de bord administrateur.
 * Sert uniquement à rediriger vers les 3 pages de gestion spécifiques.
 */
$user = $_SESSION['user'] ?? null;
?>

<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="display-6">Tableau de Bord Administrateur</h1>
        <p class="text-muted">Bonjour <?= htmlspecialchars($user['userFirstname'] ?? 'Admin') ?>, que souhaitez-vous gérer ?</p>
    </div>

    <div class="row g-4 justify-content-center">

        <!-- CARTE 1 : Vers usersPage.php -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center border-0">
                <div class="card-body">
                    <div class="display-4 text-primary mb-3"><i class="bi bi-people"></i></div>
                    <h3 class="card-title h4">Utilisateurs</h3>
                    <p class="card-text text-muted small">Gérer les employés <br> et <br> les administrateurs.</p>
                    <a href="index.php?page=usersPage" class="btn btn-outline-primary stretched-link">Gérer</a>
                </div>
            </div>
        </div>

        <!-- CARTE 2 : Vers agenciesPage.php -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center border-0">
                <div class="card-body">
                    <div class="display-4 text-success mb-3"><i class="bi bi-building"></i></div>
                    <h3 class="card-title h4">Agences</h3>
                    <p class="card-text text-muted small">Ajouter <br>ou<br> supprimer des villes/sites.</p>
                    <a href="index.php?page=agenciesPage" class="btn btn-outline-success stretched-link">Gérer</a>
                </div>
            </div>
        </div>

        <!-- CARTE 3 : Vers adminTrajetPage.php -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center border-0">
                <div class="card-body">
                    <div class="display-4 text-warning mb-3"><i class="bi bi-car-front"></i></div>
                    <h3 class="card-title h4">Trajets</h3>
                    <p class="card-text text-muted small">Voir tous les trajets <br> et <br> modérer.</p>
                    <a href="index.php?page=adminTrajets" class="btn btn-outline-warning text-dark stretched-link">Gérer</a>
                </div>
            </div>
        </div>

    </div>
</div>