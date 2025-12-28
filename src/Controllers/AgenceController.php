<?php

/**
 * Contrôleur gérant les opérations CRUD (Création, Lecture, Suppression) pour les agences.
 * Toutes les actions de ce contrôleur sont réservées à l'administrateur.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Controllers
 * @author Tony Morieux
 */

namespace Morieuxtony\MvcTest\Controllers;

use Morieuxtony\MvcTest\Models\AgenceModel;

class AgenceController
{
    /**
     * Affiche la page de gestion des agences.
     * Réservé aux administrateurs.
     * @return void
     */
    public function index(): void
    {
        \requireAdmin();


        $agencies = AgenceModel::getAgencies();

        $datas_page = [
            "description" => "Gestion des agences",
            "title" => "Agences",
            "view" => "views/pages/agenciesPage.php",
            "layout" => "views/components/layout.php",
            "agencies" => $agencies
        ];
        \drawPage($datas_page);
    }

    /**
     * Traite la soumission du formulaire d'ajout d'agence.
     */
    public function create(): void
    {
        \requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error_message'] = 'Token CSRF invalide ou expiré.';
                header("Location: index.php?page=home");
                exit;
            }


            $ville = \trim(\htmlspecialchars($_POST['ville'] ?? ''));

            if (!empty($ville)) {
                AgenceModel::createAgence($ville);
            }
        }
        header("Location: index.php?page=agenciesPage");
        exit;
    }

    /**
     * Affiche le formulaire d'édition (GET).
     * Réservé aux administrateurs.
     * @return void
     */
    public function edit(): void
    {
        \requireAdmin();

        if (!isset($_GET['id'])) {
            header("Location: index.php?page=agenciesPage");
            exit;
        }


        $id = \validateInt($_GET['id']);


        $agence = AgenceModel::getAgencyById($id);

        if (!$agence) {
            header("Location: index.php?page=agenciesPage");
            exit;
        }

        $datas_page = [
            "description" => "Modifier une agence",
            "title" => "Édition Agence",
            "view" => "views/pages/editAgencyPage.php",
            "layout" => "views/components/layout.php",
            "agence" => $agence
        ];
        \drawPage($datas_page);
    }

    /**
     * Met à jour le nom de la ville d'une agence existante.
     * Réservé aux administrateurs.
     * @return void
     */
    public function update(): void
    {
        \requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error_message'] = 'Token CSRF invalide ou expiré.';
                header("Location: index.php?page=home");
                exit;
            }

            $ville = \trim(\htmlspecialchars($_POST['ville'] ?? ''));


            $id = \validateInt($_POST['id_agence'] ?? '');

            if (!empty($ville) && $id !== null) {
                AgenceModel::updateAgence($id, $ville);
            }

            header("Location: index.php?page=agenciesPage");
            exit;
        }
    }

    /**
     * Traite la demande de suppression d'une agence.
     * Réservé aux administrateurs.
     * @return void
     */
    public function delete(): void
    {
        \requireAdmin();

        if (isset($_GET['id'])) {
            $idAgence = \validateInt($_GET['id']);
            if ($idAgence !== null) {
                AgenceModel::deleteAgence($idAgence);
            }
        }
        header("Location: index.php?page=agenciesPage");
        exit;
    }
}
