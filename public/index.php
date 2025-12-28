<?php
// Definition du chemin racine de l'application.
define('ROOT', dirname(__DIR__));

/**
 * @file index.php
 * Fichier du point d'entrée unique (Front Controller) de l'application.
 *
 * Il est responsable de l'initialisation de l'environnement, de la gestion
 * de la session, du chargement des dépendances et du routage des requêtes
 * vers les contrôleurs appropriés en fonction du paramètre 'page' de l'URL.
 *
 * @package TouchePasAuKlaxon
 * @author Tony Morieux
 * @uses vendor/autoload.php Chargement automatique des classes via composer.
 * @uses controllers/HomeController.php Contrôleur des pages génériques (accueil, connecté, admin dashboard).
 * @uses controllers/AuthController.php Contrôleur pour la gestion de l'authentification (login, logout).
 * @uses controllers/TrajetController.php Contrôleur pour la gestion des trajets.
 * @uses controllers/UserController.php Contrôleur pour la gestion des utilisateurs (réservé admin).
 * @uses controllers/AgenceController.php Contrôleur pour la gestion des agences (réservé admin).
 *
 * @var string $page La page demandée par l'utilisateur via le paramètre GET 'page'.
 * @var array $path Le chemin d'accès (utile pour un routage plus complexe).
 * @throws Exception Si la page demandée n'existe pas.
 * @return void
 */

// ------------------------------------
// 1. CHARGEMENT DES DÉPENDANCES
// ------------------------------------
require __DIR__ . '/../vendor/autoload.php';

use Morieuxtony\MvcTest\Controllers\HomeController;
use Morieuxtony\MvcTest\Controllers\AuthController;
use Morieuxtony\MvcTest\Controllers\TrajetController;
use Morieuxtony\MvcTest\Controllers\UserController;
use Morieuxtony\MvcTest\Controllers\AgenceController;


// ------------------------------------
// 2. GESTION DE LA SESSION
// ------------------------------------

//  Démarrage et Configuration sécurisée de la session
if (session_status() === PHP_SESSION_NONE) {
    // Rend le cookie de session inaccessible aux scripts JavaScript
    ini_set('session.cookie_httponly', 1);
    // Empêche l'envoi du cookie lors de requêtes cross-site
    ini_set('session.cookie_samesite', 'Strict');
    // Force la session à n'utiliser que les IDs de session générés par le système
    ini_set('session.use_strict_mode', 1);

    // En production avec HTTPS, il est fortement recommandé d'ajouter :
    // ini_set('session.cookie_secure', 1);

    session_start();
}

// ------------------------------------
// 3. ROUTAGE DES REQUÊTES
// ------------------------------------

try {
    // Détermine la page demandée, 'home' par défaut
    $page = $_GET['page'] ?? 'home';

    // Nettoyage de l'URL et extraction du premier segment pour le routage de base
    $path = explode("/", filter_var($page, FILTER_SANITIZE_URL));
    $page = $path[0];

    // Contrôle principal du flux (Switch Router)
    switch ($page) {

        // --- GÉNÉRAL (Accessible à tous ou aux connectés) ---
        case "home":
        case "accueil":
            (new HomeController())->showHome();
            break;
        case "connected":
            (new HomeController())->showConnected();
            break;
        case "admin":
            (new HomeController())->showAdminDashboard();
            break;

        // --- AUTHENTIFICATION ---
        case "login":
            // Tente la connexion si la méthode est POST, sinon affiche le formulaire
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AuthController())->login();
            } else {
                (new AuthController())->showLogin();
            }
            break;
        case "logout":
            (new AuthController())->logout();
            break;

        // --- TRAJETS ---
        case "addTrajetPage":
            (new TrajetController())->add();
            break;
        case "createTrajetAction":
            (new TrajetController())->create();
            break;
        case "editTrajetPage":
            (new TrajetController())->edit();
            break;
        case "updateTrajetAction":
            (new TrajetController())->update();
            break;
        case "deleteTrajet":
            (new TrajetController())->delete();
            break;
        case "adminTrajets":
            (new TrajetController())->adminIndex();
            break;

        // --- UTILISATEURS (Administration) ---
        case "usersPage":
            (new UserController())->index();
            break;
        case "createUserAction":
            (new UserController())->create();
            break;
        case "editUserPage":
            (new UserController())->edit();
            break;
        case "updateUserAction":
            (new UserController())->update();
            break;
        case "deleteUserAction":
            (new UserController())->delete();
            break;

        // --- AGENCES (Administration) ---
        case "agenciesPage":
            (new AgenceController())->index();
            break;
        case "createAgencyAction":
            (new AgenceController())->create();
            break;
        case "editAgencyPage":
            (new AgenceController())->edit();
            break;
        case "updateAgencyAction":
            (new AgenceController())->update();
            break;
        case "deleteAgencyAction":
            (new AgenceController())->delete();
            break;

        default:
            // Lève une exception si la page demandée n'est pas reconnue
            throw new Exception("La page demandée n'existe pas (404 Not Found)");
    }
} catch (Exception $e) {
    // Gestionnaire d'erreurs simple
    http_response_code(500); // Code d'erreur serveur
    echo "<h1>Erreur Interne</h1>";
    echo "<p>Une erreur est survenue : " . htmlspecialchars($e->getMessage()) . "</p>";
    // En environnement de production, masquer les détails de l'erreur
}
