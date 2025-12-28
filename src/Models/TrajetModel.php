<?php

/**
 * Fichier contenant toutes les fonctions liées à la table "Trajet" (Modèle).
 * Fournit les opérations de lecture et d'écriture (CRUD) pour les trajets.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Models
 * @author Tony Morieux
 */

namespace Morieuxtony\MvcTest\Models;

use PDO;

use function Morieuxtony\MvcTest\Config\getDatabase;

class TrajetModel
{
    /**
     * Récupère tous les trajets de la base de données, sans aucun filtre.
     * Principalement utilisé pour des besoins d'administration ou de debug.
     *
     * @return array Une liste de tous les trajets.
     */
    public static function getAllTrajets()
    {
        $req = "SELECT * FROM trajet";
        $stmt = getDatabase()->prepare($req);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $datas;
    }

    /**
     * Récupère les trajets futurs ayant des places disponibles, en y joignant les détails du conducteur.
     * C'est la fonction principale pour l'affichage public.
     *
     * @return array Une liste des trajets pertinents avec les informations du conducteur.
     */
    public static function getAllTrajetsWithConductorDetails()
    {
        $req = "SELECT t.*, 
                   u.prenom_utilisateur, 
                   u.nom_utilisateur, 
                   u.telephone, 
                   u.email 
            FROM Trajet t
            JOIN Utilisateur u ON t.Id_Conducteur = u.Id_Utilisateur
            WHERE t.nb_places_dispo > 0 
            AND t.date_heure_depart >= NOW()
            ORDER BY t.date_heure_depart ASC";

        $stmt = getDatabase()->prepare($req);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $datas;
    }

    /**
     * Crée un nouveau trajet dans la base de données.
     *
     * @param int    $ag_dep         L'ID de l'agence de départ.
     * @param string $date_dep       La date et heure de départ (format Y-m-d H:i:s).
     * @param int    $ag_arr         L'ID de l'agence d'arrivée.
     * @param string $date_arr       La date et heure d'arrivée (format Y-m-d H:i:s).
     * @param int    $nb_places      Le nombre total de places.
     * @param int    $id_conducteur  L'ID de l'utilisateur qui crée le trajet.
     * @return void                  Cette fonction n'a pas de valeur de retour.
     */
    public static function createTrajet($ag_dep, $date_dep, $ag_arr, $date_arr, $nb_places, $id_conducteur)
    {
        $db = getDatabase();

        $sql = "INSERT INTO Trajet 
            (Id_Agence_Depart,
            date_heure_depart,
            Id_Agence_Arrivee,
            date_heure_arrivee,
            nb_places_total,
            nb_places_dispo,
            Id_Conducteur) 
            VALUES 
            (:ag_dep, :date_dep, :ag_arr, :date_arr, :nb_total, :nb_dispo, :conducteur)";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':ag_dep'     => $ag_dep,
            ':date_dep'   => $date_dep,
            ':ag_arr'     => $ag_arr,
            ':date_arr'   => $date_arr,
            ':nb_total'   => $nb_places,
            ':nb_dispo'   => $nb_places,
            ':conducteur' => $id_conducteur
        ]);
    }

    /**
     * Récupère un trajet spécifique par son identifiant.
     *
     * @param int $id_trajet L'identifiant unique du trajet.
     * @return array|false   Le tableau associatif du trajet si trouvé, sinon false.
     */
    public static function getTrajetById($id_trajet)
    {
        $req = "SELECT * FROM trajet WHERE Id_Trajet = :Id_Trajet";
        $stmt = getDatabase()->prepare($req);
        $stmt->bindParam(':Id_Trajet', $id_trajet, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    /**
     * Met à jour les informations d'un trajet existant.
     *
     * @param int    $id_trajet        L'ID du trajet à modifier.
     * @param int    $agence_depart    Le nouvel ID de l'agence de départ.
     * @param string $date_depart      La nouvelle date de départ.
     * @param int    $agence_arrivee   Le nouvel ID de l'agence d'arrivée.
     * @param string $date_arrivee     La nouvelle date d'arrivée.
     * @param int    $nb_places        Le nouveau nombre total de places.
     * @return bool                    True si la mise à jour a réussi, false sinon.
     */
    public static function updateTrajet(
        $id_trajet,
        $agence_depart,
        $date_depart,
        $agence_arrivee,
        $date_arrivee,
        $nb_places
    ) {
        $req = "UPDATE trajet 
            SET Id_Agence_Depart = :Id_Agence_Depart,
                date_heure_depart = :date_heure_depart,
                Id_Agence_Arrivee = :Id_Agence_Arrivee,
                date_heure_arrivee = :date_heure_arrivee,
                nb_places_total = :nb_places_total,
                nb_places_dispo = :nb_places_dispo
            WHERE Id_Trajet = :Id_Trajet";

        $stmt = getDatabase()->prepare($req);
        $stmt->bindParam(':Id_Agence_Depart', $agence_depart, PDO::PARAM_INT);
        $stmt->bindParam(':date_heure_depart', $date_depart);
        $stmt->bindParam(':Id_Agence_Arrivee', $agence_arrivee, PDO::PARAM_INT);
        $stmt->bindParam(':date_heure_arrivee', $date_arrivee);
        $stmt->bindParam(':nb_places_total', $nb_places, PDO::PARAM_INT);
        $stmt->bindParam(':nb_places_dispo', $nb_places, PDO::PARAM_INT); // On réinitialise aussi les places dispo
        $stmt->bindParam(':Id_Trajet', $id_trajet, PDO::PARAM_INT);

        $result = $stmt->execute();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * Supprime un trajet de la base de données.
     *
     * @param int $id_trajet L'identifiant du trajet à supprimer.
     * @return void
     */
    public static function deleteTrajet($id_trajet)
    {
        $db = getDatabase();
        $sql = "DELETE FROM Trajet WHERE Id_Trajet = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id_trajet, PDO::PARAM_INT);
        $stmt->execute();
    }
}
