<?php

/**
 * @file AuthController.php
 * Fichier du contrôleur gérant l'authentification des utilisateurs.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Controllers
 * @author Tony Morieux
 */

namespace Morieuxtony\MvcTest\Controllers;

use Morieuxtony\MvcTest\Models\UserModel;

// Ajoutez les imports pour les fonctions du modèle

/**
 * Class AuthController
 * Gère toutes les logiques liées à l'authentification des utilisateurs,
 * y compris l'affichage du formulaire de connexion, le processus de connexion
 * et la déconnexion.
 * Fournit des méthodes pour sécuriser l'accès aux parties restreintes du site.
 * Utilise des pratiques de sécurité telles que la vérification CSRF et la gestion des sessions.
 */
class AuthController
{
    /**
     * Affiche le formulaire de connexion.
     * Si un utilisateur est déjà connecté, il est redirigé vers la page "connected".
     * Prépare les données nécessaires pour l'affichage de la page de connexion.
     *
     * @return void
     */
    public function showLogin()
    {
        // Si déjà connecté, on redirige
        if (isset($_SESSION['user'])) {
            // Si c'est un admin, vers la page admin, sinon vers connected
            if (isset($_SESSION['user']['admin']) && $_SESSION['user']['admin'] == 1) {
                header("Location: index.php?page=admin");
            } else {
                header("Location: index.php?page=connected");
            }
            exit;
        }


        $datas_page = [
            "description" => "Page de connexion",
            "title" => "Connexion",
            "view" => "views/pages/loginPage.php",
            "layout" => "views/components/layout.php",
        ];
        drawPage($datas_page);
    }

    /**
     * Gère la logique de connexion de l'utilisateur.
     * Traite les données soumises via le formulaire de connexion (méthode POST).
     * Effectue les vérifications CSRF, la validation des champs et la tentative de connexion.
     * Redirige l'utilisateur en fonction du succès ou de l'échec de la connexion.
     *
     * @uses $_POST['csrf_token'] pour la vérification CSRF.
     * @uses $_POST['username'] pour l'adresse email de l'utilisateur.
     * @uses $_POST['password'] pour le mot de passe de l'utilisateur.
     * @return void
     */
    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // 1. Vérification CSRF
            // Si le token n'est pas bon, on arrête tout par sécurité
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error_message'] = 'Token CSRF invalide ou expiré.';
                header("Location: index.php?page=home");
                exit;
            }

            $email = trim($_POST["username"] ?? "");
            $password = trim($_POST["password"] ?? "");

            // 2. Vérification champs vides
            if (empty($email) || empty($password)) {
                // On redirige avec une erreur spécifique
                header("Location: index.php?page=login&error=empty");
                exit();
            }

            // 3. Tentative de connexion
            if (UserModel::loginUser($email, $password)) {
                // Sécurité : Régénérer l'ID de session
                session_regenerate_id(true);

                // 4. Redirection selon le role
                if (isset($_SESSION['user']['admin']) && $_SESSION['user']['admin'] == 1) {
                    header("Location: index.php?page=admin");
                } else {
                    header("Location: index.php?page=connected");
                }
                exit();
            } else {
                // Erreur login (mauvais mot de passe ou email)
                header("Location: index.php?page=login&error=bad_credentials");
                exit();
            }
        }
    }

    /**
     * Gère la déconnexion de l'utilisateur.
     * Détruit la session de l'utilisateur et redirige vers la page d'accueil ou de connexion.
     *
     * @return void
     */
    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        // Il est souvent plus propre de rediriger vers le login ou la home après déconnexion
        header("Location: index.php?page=home");
        exit;
    }
}
