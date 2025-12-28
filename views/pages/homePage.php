<?php

/**
 * @file homePage.php
 * Fichier de la page d'accueil principale de l'application.
 *
 * Cette page sert de point d'entrée et gère l'affichage initial pour tous les utilisateurs.
 * Elle détermine l'état de l'utilisateur (connecté, administrateur) en se basant sur les
 * informations de session. Un message est affiché aux visiteurs non connectés,
 * puis le contenu principal est chargé via une inclusion de fichier.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 *
 * @uses $_GET['page'] Pour déterminer la page actuellement demandée.
 * @uses $_SESSION['user'] Pour récupérer les informations de l'utilisateur connecté.
 * @uses "views/components/mainContent.php" Le fichier qui contient l'affichage principal du contenu.
 */

// Récupère la page courante depuis l'URL, ou une chaîne vide par défaut.
$currentPage = $_GET['page'] ?? '';

// Récupère les informations de l'utilisateur depuis la session, ou null si non définies.
$user = $_SESSION['user'] ?? null;

// Vérifie si un utilisateur est considéré comme connecté.
$isConnected = ($user && isset($user['userLastname']));

// Vérifie si l'utilisateur connecté a les droits d'administrateur.
$isAdmin = ($isConnected && isset($user['admin']) && $user['admin'] == "1");
?>

<?php if ($isConnected): ?>
    <!-- Titre caché, potentiellement pour l'accessibilité ou le SEO pour les utilisateurs connectés -->
    <h1 class="hidden"></h1>
<?php endif; ?>

<!-- Message affiché à tous les utilisateurs, les invitant à se connecter pour plus de détails -->
<h1 class="text-center mb-4">Pour obtenir plus d'informations sur un trajet, veuillez vous connecter</h1>

<!-- Inclusion du contenu principal de la page (ex: liste des trajets) -->
<?php
require_once(ROOT . "/views/components/mainContent.php");
?>