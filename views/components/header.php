<?php

/**
 * @file header.php
 * Fichier du composant d'en-tête (header) de l'application.
 *
 * Gère l'affichage de la barre de navigation principale en fonction de l'état de connexion de l'utilisateur
 * etde ses droits (administrateur ou utilisateur standard).
 * Inclut des liens de navigation conditionnels pour l'accueil, la création de trajet, la gestion des utilisateurs,
 * des agences et des trajets pour les administrateurs, ainsi que les options de connexion/déconnexion.
 * Utilise les information de session pour personnaliser l'expérience utilisateur.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 */

// Récupère la page courante depuis l'URL, par défaut une chaîne vide.
$currentPage = $_GET['page'] ?? '';

// Récupère les informations de l'utilisateur depuis la session. Null si non connecté.
$user = $_SESSION['user'] ?? null;

// Vérifie si l'utilisateur est connecté en s'assurant que la variable de session 'user' existe et contient un nom.
$isConnected = ($user && isset($user['userLastname']));

// Vérifie si l'utilisateur connecté est un administrateur.
$isAdmin = ($isConnected && isset($user['admin']) && $user['admin'] == "1");
?>

<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Logo ou titre du site, redirige vers 'connected' si l'utilisateur est connecté, page 'adminPage' si un administrateur est connecté, sinon vers 'home' -->
            <a class="navbar-brand ms-5" href="index.php?page=<?= $isAdmin ? 'admin' : ($isConnected ? 'connected' : 'home') ?>">Touche pas au Klaxon !!</a>

            <!-- Bouton du "hamburger" pour la navigation mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Conteneur des éléments de navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <!-- Liens de navigation spécifiques à l'administrateur -->
                    <?php if ($isAdmin): ?>
                        <li class="nav-item"><a class="nav-link adminUsers" href="index.php?page=usersPage">Utilisateurs</a></li>
                        <li class="nav-item"><a class="nav-link adminAgencies" href="index.php?page=agenciesPage">Agences</a></li>
                        <li class="nav-item"><a class="nav-link adminTrajets" href="index.php?page=adminTrajets">Trajets</a></li>
                    <?php endif; ?>

                    <!-- Liens de navigation spécifiques à l'utilisateur connecté -->
                    <?php if ($isConnected): ?>

                        <?php if (!$isAdmin): /* Affiché seulement si l'utilisateur est connecté MAIS PAS administrateur */ ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=addTrajetPage">Créer d'un trajet</a>
                            </li>
                        <?php endif; ?>

                        <!-- Message de bienvenue personnalisé pour l'utilisateur connecté -->
                        <li class="nav-item">
                            <span class="nav-link fw-bold">
                                <!-- Affiche le prénom et le nom de l'utilisateur en toute sécurité -->
                                Bonjour <?= escape($user['userFirstname']) . ' ' . escape($user['userLastname']) ?>
                            </span>
                        </li>

                        <!-- Lien de déconnexion -->
                        <li class="nav-item">
                            <a class="nav-link btn btn-link text-secondary" href="index.php?page=logout">Déconnexion</a>
                        </li>
                    <?php endif; ?>

                    <!-- Lien de connexion, affiché uniquement si l'utilisateur n'est pas connecté et n'est pas déjà sur la page de connexion -->
                    <?php if (!$isConnected && $currentPage !== 'login'): ?>
                        <li class="nav-item d-flex align-items-center me-5">
                            <a class="btn btn-primary text-white ms-2" href="index.php?page=login">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>