<?php

/**
 * @file UserController.php
 * Fichier du contrôleur gérant toutes les opérations relatives aux utilisateurs.
 * Cela inclut l'affichage de la liste des utilisateurs pour l'administration,
 * la création, l'édition et la suppression d'utilisateurs.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Controllers
 * @author Tony Morieux
 */

namespace Morieuxtony\MvcTest\Controllers;

use Morieuxtony\MvcTest\Models\AgenceModel;
use Morieuxtony\MvcTest\Models\UserModel;

/**
 * Class UserController
 * Gère les interactions et la logique métier liées aux utilisateurs.
 * Fournit des méthodes pour administrer les utilisateurs du système,
 * notamment les fonctionnalités CRUD (Créer, Lire, Mettre à jour, Supprimer).
 * L'accès à la plupart de ces fonctions est restreint aux administrateurs.
 */
class UserController
{
    public function showChangeMdp()
    {
        // On génère le token CSRF avant d'afficher la page
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $datas_page = [
            "description" => "Changer mot de passe",
            "title" => "Première connexion",
            "view" => "views/pages/changeMdpPage.php",
            "layout" => "views/components/layout.php",
        ];


        \drawPage($datas_page);
    }

    /**
     * Affiche la liste de tous les utilisateurs pour l'administration.
     *
     * Cette méthode est accessible uniquement aux utilisateurs ayant le statut d'administrateur.
     * Elle récupère tous les utilisateurs et toutes les agences pour pouvoir afficher
     * les informations complètes des utilisateurs, y compris le nom de leur agence.
     * Prépare les données pour la fonction `drawPage()`.
     *
     * @uses $_SESSION['user'] Pour vérifier les droits d'administrateur de l'utilisateur connecté.
     * @uses getAllUsers() Pour récupérer la liste de tous les utilisateurs.
     * @uses getAgencies() Pour récupérer la liste de toutes les agences.
     * @return void
     */
    public function index()
    {
        // 1. Sécurité : Admin seulement
        if (!isset($_SESSION['user']) || $_SESSION['user']['admin'] != 1) {
            header("Location: index.php?page=home");
            exit;
        }

        // 2. Récupération des données
        $users = UserModel::getAllUsers();
        $agencies = AgenceModel::getAgencies();

        // 3. Mapping des agences par ID pour faciliter l'affichage de la ville
        $agencies_by_id = [];
        foreach ($agencies as $a) {
            $agencies_by_id[$a['Id_Agence']] = $a['ville'];
        }

        // 4. Préparation des données pour la vue et affichage
        $datas_page = [
            "description" => "Gestion utilisateurs",
            "title" => "Utilisateurs",
            "view" => "views/pages/usersPage.php",
            "layout" => "views/components/layout.php",
            "users" => $users,
            "agencies_by_id" => $agencies_by_id
        ];
        \drawPage($datas_page);
    }

    /**
     * Traite la soumission du formulaire de création d'un nouvel utilisateur (méthode POST).
     *
     * Vérifie le token CSRF pour des raisons de sécurité.
     * Récupère les données du formulaire, hache le mot de passe avant de l'enregistrer.
     * Détermine si l'utilisateur est administrateur en fonction de la case à cocher.
     * Crée le nouvel utilisateur dans la base de données.
     * Redirige l'utilisateur vers la page de gestion des utilisateurs après la création.
     *
     * @uses $_SERVER['REQUEST_METHOD'] Pour vérifier si la requête est de type POST.
     * @uses $_POST['csrf_token'] Pour la vérification CSRF.
     * @uses $_POST['mdp'] Le mot de passe de l'utilisateur (sera haché).
     * @uses $_POST['is_admin'] Indique si l'utilisateur doit être administrateur.
     * @uses $_POST['nom'] Le nom de l'utilisateur.
     * @uses $_POST['prenom'] Le prénom de l'utilisateur.
     * @uses $_POST['email'] L'adresse e-mail de l'utilisateur.
     * @uses $_POST['tel'] Le numéro de téléphone de l'utilisateur.
     * @uses $_POST['id_agence'] L'ID de l'agence à laquelle l'utilisateur est rattaché.
     * @uses password_hash() Pour hacher le mot de passe.
     * @uses createUser() Pour insérer le nouvel utilisateur en base de données.
     * @return void
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error_message'] = 'Token CSRF invalide ou expiré.';
                header("Location: index.php?page=home");
                exit;
            }

            // Logique de création (Hash password, etc.)
            $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
            $admin = isset($_POST['is_admin']) ? 1 : 0;
            UserModel::createUser(
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['tel'],
                $mdp,
                $admin,
                $_POST['id_agence']
            );
            header("Location: index.php?page=usersPage");
            exit;
        }
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur.
     *
     * Cette méthode est un marqueur pour la logique future.
     * Elle devrait permettre de récupérer les informations d'un utilisateur spécifique
     * et de les pré-remplir dans un formulaire d'édition.
     *
     * @todo Implémenter la logique pour afficher le formulaire d'édition d'un utilisateur.
     * @return void
     */
    public function edit()
    {

        // 1. Sécurité : Admin seulement
        if (!isset($_SESSION['user']) || $_SESSION['user']['admin'] != 1) {
            header("Location: index.php?page=home");
            exit;
        }

        // 2. Vérifier l'ID de l'utilisateur
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            // Rediriger ou afficher une erreur si l'ID est manquant ou invalide
            header("Location: index.php?page=usersPage");
            exit;
        }

        $userId = (int)$_GET['id'];
        $user = UserModel::getUserById($userId);

        if (!$user) {
            // L'utilisateur n'existe pas, rediriger
            header("Location: index.php?page=usersPage");
            exit;
        }

        // 3. Récupération des données nécessaires pour le formulaire (agences)
        $agencies = AgenceModel::getAgencies(); // Assurez-vous que cette fonction existe dans agenceModel.php

        // 4. Préparation des données pour la vue et affichage
        $datas_page = [
            "description" => "Édition utilisateur",
            "title" => "Éditer l'utilisateur",
            "view" => "views/pages/editUserPage.php", // Créez cette vue
            "layout" => "views/components/layout.php",
            "user" => $user,
            "agencies" => $agencies
        ];
        \drawPage($datas_page);
    }

    /**
     * Traite la soumission du formulaire de mise à jour d'un utilisateur (méthode POST).
     *
     * Cette méthode est un marqueur pour la logique future.
     * Elle devrait permettre de traiter les données soumises par le formulaire d'édition
     * et de mettre à jour les informations de l'utilisateur dans la base de données.
     *
     * @todo Implémenter la logique pour mettre à jour les informations d'un utilisateur.
     * @return void
     */
    public function update()
    {
        // 1. Sécurité : Admin seulement et vérification POST
        if (!isset($_SESSION['user']) || $_SESSION['user']['admin'] != 1) {
            header("Location: index.php?page=home");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error_message'] = 'Token CSRF invalide ou expiré.';
                header("Location: index.php?page=home");
                exit;
            }

            // 2. Vérifier l'ID de l'utilisateur
            if (!isset($_POST['id_user']) || !is_numeric($_POST['id_user'])) {
                header("Location: index.php?page=usersPage");
                exit;
            }
            $userId = (int)$_POST['id_user'];

            $admin = isset($_POST['is_admin']) ? 1 : 0;

            // 3. Appel de la fonction de mise à jour du modèle
            UserModel::updateUser(
                $userId,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['tel'],
                $admin,
                $_POST['id_agence']
            );

            header("Location: index.php?page=usersPage");
            exit;
        } else {
            // Si la requête n'est pas POST, rediriger
            header("Location: index.php?page=usersPage");
            exit;
        }
    }


    /**
     * Gère la suppression d'un utilisateur.
     *
     * Récupère l'ID de l'utilisateur à supprimer depuis les paramètres GET.
     * Supprime l'utilisateur correspondant de la base de données.
     * Redirige l'utilisateur vers la page de gestion des utilisateurs après la suppression.
     * Une vérification des droits d'administrateur serait essentielle ici.
     *
     * @uses $_GET['id'] L'ID de l'utilisateur à supprimer.
     * @uses deleteUser() Pour supprimer l'utilisateur de la base de données.
     * @return void
     */
    public function delete()
    {
        if (isset($_GET['id'])) {
            UserModel::deleteUser((int)$_GET['id']);
        }
        header("Location: index.php?page=usersPage");
        exit;
    }
}
