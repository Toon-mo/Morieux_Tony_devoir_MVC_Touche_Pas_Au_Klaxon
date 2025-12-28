<?php

/**
 * @file HomeController.php
 * Fichier du contrôleur gérant l'affichage des différentes pages du site.
 * Cela inclut la page d'accueil, la page utilisateur connectée,
 * et la page d'administration.
 */

namespace Morieuxtony\MvcTest\Controllers;

use Morieuxtony\MvcTest\Models\TrajetModel;
use Morieuxtony\MvcTest\Models\AgenceModel;
use Morieuxtony\MvcTest\Models\UserModel;

/**
 * Class HomeController
 */
class HomeController
{
    /**
     * Affiche la page d'accueil du site.
     * @return void
     */
    public function showHome()
    {
        $users = UserModel::getAllUsers();
        $agencies = AgenceModel::getAgencies();
        $trajets = TrajetModel::getAllTrajetsWithConductorDetails();

        $agencies_by_id = [];
        foreach ($agencies as $agency) {
            $agencies_by_id[$agency['Id_Agence']] = $agency['ville'];
        }

        $datas_page = [
            "description" => "Page d'accueil du site",
            "title" => "Accueil",
            "view" => "views/pages/homePage.php",
            "layout" => "views/components/layout.php",
            "trajets" => $trajets,
            "agencies_by_id" => $agencies_by_id,
            "users" => $users
        ];

        \drawPage($datas_page);
    }

    /**
     * Affiche la page de l'espace utilisateur connecté.
     * @return void
     */
    public function showConnected()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $users = UserModel::getAllUsers();
        $agencies = AgenceModel::getAgencies();
        $trajets = TrajetModel::getAllTrajetsWithConductorDetails();

        $agencies_by_id = [];
        foreach ($agencies as $agency) {
            $agencies_by_id[$agency['Id_Agence']] = $agency['ville'];
        }

        $datas_page = [
            "description" => "Espace utilisateur connecté",
            "title" => "Mon Espace",
            "view" => "views/pages/connectPage.php",
            "layout" => "views/components/layout.php",
            "trajets" => $trajets,
            "agencies_by_id" => $agencies_by_id,
            "users" => $users
        ];

        \drawPage($datas_page);
    }

    /**
     * Affiche le tableau de bord administrateur.
     * @return void
     */
    public function showAdminDashboard()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['admin'] != 1) {
            header("Location: index.php?page=home");
            exit;
        }

        $datas_page = [
            "description" => "Tableau de bord administrateur",
            "title" => "Admin Dashboard",
            "view" => "views/pages/adminPage.php",
            "layout" => "views/components/layout.php",
        ];

        \drawPage($datas_page);
    }
}
