<?php

/**
 * @file TrajetController.php
 * Fichier du contrôleur gérant toutes les opérations relatives aux trajets.
 * Cela inclut la création, l'édition, la suppression et l'affichage des trajets,
 * ainsi que la gestion de l'accès aux fonctionnalités pour les utilisateurs et les administrateurs.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Controllers
 * @author Tony Morieux
 */

namespace Morieuxtony\MvcTest\Controllers;

use Morieuxtony\MvcTest\Models\TrajetModel;
use Morieuxtony\MvcTest\Models\AgenceModel;

use function Morieuxtony\MvcTest\Config\{verifyCSRFToken, validateInt, validateDateTime, validateDateOrder};

/**
 * Class TrajetController
 * Gère les interactions et la logique métier liées aux trajets.
 * Fournit des méthodes pour afficher les formulaires, traiter les soumissions,
 * et gérer les droits d'accès.
 * L'accès à certaines fonctions est restreint aux administrateurs ou aux auteurs des trajets.
 */
class TrajetController
{
    /**
     * Affiche le formulaire de création d'un nouveau trajet.
     * @return void
     */
    public function add()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $agencies = AgenceModel::getAgencies();
        $datas_page = [
            "description" => "Créer un trajet",
            "title" => "Nouveau Trajet",
            "view" => "views/pages/addTrajetPage.php",
            "layout" => "views/components/layout.php",
            "agencies" => $agencies,
        ];
        \drawPage($datas_page);
    }

    /**
     * Traite la soumission du formulaire de création de trajet (méthode POST).
     * @return void
     */
    public function create()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification CSRF
            if (!\verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error_message'] = 'Erreur de sécurité : token CSRF invalide';
                header("Location: index.php?page=addTrajetPage");
                exit;
            }

            // Récupération des données
            $agenceDepart = \validateInt($_POST['agence_depart'] ?? '');
            $dateDepart = $_POST['date_depart'] ?? '';
            $agenceArrivee = \validateInt($_POST['agence_arrivee'] ?? '');
            $dateArrivee = $_POST['date_arrivee'] ?? '';
            $nbPlaces = \validateInt($_POST['nb_places'] ?? '');

            // Validation de base
            if (!$agenceDepart || !$agenceArrivee || !$nbPlaces) {
                $_SESSION['error_message'] = 'Tous les champs sont obligatoires.';
                header("Location: index.php?page=addTrajetPage");
                exit;
            }

            // Validation de la date de départ
            if (!\validateDateTime($dateDepart)) {
                $_SESSION['error_message'] = 'La date de départ est invalide ou dans le passé.';
                header("Location: index.php?page=addTrajetPage");
                exit;
            }

            // Validation de la date d'arrivée
            if (!\validateDateTime($dateArrivee)) {
                $_SESSION['error_message'] = 'La date d\'arrivée est invalide ou dans le passé.';
                header("Location: index.php?page=addTrajetPage");
                exit;
            }

            // Validation de l'ordre des dates
            if (!\validateDateOrder($dateDepart, $dateArrivee)) {
                $_SESSION['error_message'] = 'La date d\'arrivée doit être postérieure à la date de départ.';
                header("Location: index.php?page=addTrajetPage");
                exit;
            }

            // Validation : départ et arrivée doivent être différents
            if ($agenceDepart === $agenceArrivee) {
                $_SESSION['error_message'] = 'L\'agence de départ et d\'arrivée doivent être différentes.';
                header("Location: index.php?page=addTrajetPage");
                exit;
            }

            // Validation du nombre de places
            if ($nbPlaces < 1 || $nbPlaces > 8) {
                $_SESSION['error_message'] = 'Le nombre de places doit être entre 1 et 8.';
                header("Location: index.php?page=addTrajetPage");
                exit;
            }
            // Vérifier que les agences existent
            $agenceDepart = AgenceModel::getAgencyById($agenceDepart);
            $agenceArrivee = AgenceModel::getAgencyById($agenceArrivee);

            if (!$agenceDepart || !$agenceArrivee) {
                $_SESSION['error_message'] = 'Agence invalide.';
                header("Location: index.php?page=addTrajetPage");
                exit;
            }

            // Vérifier durée minimum du trajet (60 minutes temps approximatif de la plus courte distance)
            $depart = new \DateTime($dateDepart);
            $arrivee = new \DateTime($dateArrivee);
            $duree = $arrivee->getTimestamp() - $depart->getTimestamp();

            if ($duree < 3600) { // 60 minutes
                $_SESSION['error_message'] = 'Le trajet doit durer au moins 60 minutes.';
                header("Location: index.php?page=addTrajetPage");
                exit;
            }

            // Création du trajet
            TrajetModel::createTrajet(
                $agenceDepart['Id_Agence'],
                $dateDepart,
                $agenceArrivee['Id_Agence'],
                $dateArrivee,
                $nbPlaces,
                $_SESSION['user']['id']
            );

            $_SESSION['success_message'] = 'Trajet créé avec succès !';
            header("Location: index.php?page=connected");
            exit;
        }
    }

    /**
     * Affiche le formulaire d'édition d'un trajet existant.
     * Seul l'auteur du trajet peut y accéder.
     * @return void
     */
    public function edit()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        if (!isset($_GET['id'])) {
            header("Location: index.php?page=connected");
            exit;
        }

        $trajetId = \validateInt($_GET['id']);
        if (!$trajetId) {
            header("Location: index.php?page=connected");
            exit;
        }

        $trajet = TrajetModel::getTrajetById($trajetId);

        if (!$trajet) {
            $_SESSION['error_message'] = 'Trajet introuvable.';
            header("Location: index.php?page=connected");
            exit;
        }

        // Vérification : seul l'auteur peut éditer
        if ($trajet['Id_Conducteur'] != $_SESSION['user']['id']) {
            $_SESSION['error_message'] = 'Vous n\'êtes pas autorisé à modifier ce trajet.';
            header("Location: index.php?page=connected");
            exit;
        }

        $agencies = AgenceModel::getAgencies();
        $datas_page = [
            "description" => "Modifier trajet",
            "title" => "Modification",
            "view" => "views/pages/editTrajetPage.php",
            "layout" => "views/components/layout.php",
            "trajet" => $trajet,
            "agencies" => $agencies
        ];
        \drawPage($datas_page);
    }

    /**
     * Traite la soumission du formulaire de mise à jour d'un trajet (méthode POST).
     * Seul l'auteur du trajet peut effectuer cette opération.
     * @return void
     */
    public function update()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification CSRF
            if (!\verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error_message'] = 'Erreur de sécurité : token CSRF invalide.';
                header("Location: index.php?page=connected");
                exit;
            }

            // Récupération des données du formulaire
            $idTrajet = \validateInt($_POST['Id_Trajet'] ?? '');
            $agenceDepart = \validateInt($_POST['agence_depart'] ?? '');
            $dateDepart = $_POST['date_depart'] ?? '';
            $agenceArrivee = \validateInt($_POST['agence_arrivee'] ?? '');
            $dateArrivee = $_POST['date_arrivee'] ?? '';
            $nbPlaces = \validateInt($_POST['nb_places'] ?? '');

            // Validation de base
            if (!$idTrajet || !$agenceDepart || !$agenceArrivee || !$nbPlaces) {
                $_SESSION['error_message'] = 'Données invalides.';
                header("Location: index.php?page=editTrajetPage&id=" . $idTrajet);
                exit;
            }

            // Vérification des droits : seul l'auteur peut modifier son trajet
            $trajet = TrajetModel::getTrajetById($idTrajet);
            if (!$trajet || $trajet['Id_Conducteur'] != $_SESSION['user']['id']) {
                $_SESSION['error_message'] = 'Vous n\'êtes pas autorisé à modifier ce trajet.';
                header("Location: index.php?page=connected");
                exit;
            }

            // Validation de la date de départ
            if (!\validateDateTime($dateDepart)) {
                $_SESSION['error_message'] = 'La date de départ est invalide ou dans le passé.';
                header("Location: index.php?page=editTrajetPage&id=" . $idTrajet);
                exit;
            }

            // Validation de la date d'arrivée
            if (!\validateDateTime($dateArrivee)) {
                $_SESSION['error_message'] = 'La date d\'arrivée est invalide ou dans le passé.';
                header("Location: index.php?page=editTrajetPage&id=" . $idTrajet);
                exit;
            }

            // Validation de l'ordre des dates
            if (!\validateDateOrder($dateDepart, $dateArrivee)) {
                $_SESSION['error_message'] = 'La date d\'arrivée doit être postérieure à la date de départ.';
                header("Location: index.php?page=editTrajetPage&id=" . $idTrajet);
                exit;
            }

            // Validation : départ et arrivée doivent être différents
            if ($agenceDepart === $agenceArrivee) {
                $_SESSION['error_message'] = 'L\'agence de départ et d\'arrivée doivent être différentes.';
                header("Location: index.php?page=editTrajetPage&id=" . $idTrajet);
                exit;
            }

            // Validation du nombre de places
            if ($nbPlaces < 1 || $nbPlaces > 8) {
                $_SESSION['error_message'] = 'Le nombre de places doit être entre 1 et 8.';
                header("Location: index.php?page=editTrajetPage&id=" . $idTrajet);
                exit;
            }

            // Mise à jour du trajet
            TrajetModel::updateTrajet($idTrajet, $agenceDepart, $dateDepart, $agenceArrivee, $dateArrivee, $nbPlaces);

            $_SESSION['success_message'] = 'Trajet modifié avec succès !';
            header("Location: index.php?page=connected");
            exit;
        }
    }

    /**
     * Gère la suppression d'un trajet.
     * Seul l'auteur du trajet ou un administrateur peut effectuer cette opération.
     * @return void
     */
    public function delete()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $id = \validateInt($_GET['id'] ?? '');
        if (!$id) {
            $_SESSION['error_message'] = 'ID de trajet invalide.';
            header("Location: index.php?page=connected");
            exit;
        }

        $trajet = TrajetModel::getTrajetById($id);
        if (!$trajet) {
            $_SESSION['error_message'] = 'Trajet introuvable.';
            header("Location: index.php?page=connected");
            exit;
        }

        $isAuteur = ($trajet['Id_Conducteur'] == $_SESSION['user']['id']);
        $isAdmin = ($_SESSION['user']['admin'] == 1);

        if ($isAuteur || $isAdmin) {
            TrajetModel::deleteTrajet($id);
            $_SESSION['success_message'] = 'Trajet supprimé avec succès !';
        } else {
            $_SESSION['error_message'] = 'Vous n\'êtes pas autorisé à supprimer ce trajet.';
        }

        header("Location: index.php?page=connected");
        exit;
    }

    /**
     * Affiche la page d'administration des trajets.
     * Seuls les administrateurs peuvent y accéder.
     * @return void
     */
    public function adminIndex()
    {
        // Sécurité : Admin seulement
        if (!isset($_SESSION['user']) || $_SESSION['user']['admin'] != 1) {
            header("Location: index.php?page=home");
            exit;
        }

        // Récupération des données
        $trajets = TrajetModel::getAllTrajetsWithConductorDetails();
        $agencies = AgenceModel::getAgencies();

        // Mapping des villes
        $agencies_by_id = [];
        foreach ($agencies as $agency) {
            $agencies_by_id[$agency['Id_Agence']] = $agency['ville'];
        }

        // Affichage
        $datas_page = [
            "description" => "Administration des trajets",
            "title" => "Admin Trajets",
            "view" => "views/pages/adminTrajetPage.php",
            "layout" => "views/components/layout.php",
            "trajets" => $trajets,
            "agencies_by_id" => $agencies_by_id
        ];
        \drawPage($datas_page);
    }
}
