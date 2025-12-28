<?php

/**
 * Fichier contenant toutes les fonctions liées à la table "Agence" (Modèle).
 * Fournit les opérations de lecture et d'écriture (CRUD) pour les agences (villes).
 *
 * @package TouchePasAuKlaxon
 * @subpackage Models
 * @author Tony Morieux
 */

namespace Morieuxtony\MvcTest\Models;

use PDO;

use function Morieuxtony\MvcTest\Config\getDatabase;

class AgenceModel
{
    /**
     * Crée une nouvelle agence (ville) dans la base de données.
     *
     * @param string $ville Le nom de la ville à ajouter.
     * @return void
     */
    public static function createAgence($ville)
    {
        $db = getDatabase();
        $sql = "INSERT INTO Agence (ville) VALUES (:ville)";
        $stmt = $db->prepare($sql);
        $stmt->execute([':ville' => $ville]);
    }

    /**
     * Récupère la liste complète de toutes les agences.
     *
     * @return array Une liste de toutes les agences, chacune étant un tableau associatif.
     */
    public static function getAgencies()
    {
        $req = "SELECT * FROM agence";
        $stmt = getDatabase()->prepare($req);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $datas;
    }

    /**
     * Récupère une agence spécifique par son identifiant.
     *
     * @param int $id L'identifiant unique de l'agence.
     * @return array|false Le tableau associatif de l'agence si trouvée, sinon false.
     */
    public static function getAgencyById($id)
    {
        $req = "SELECT * FROM Agence WHERE Id_Agence = :id";
        $stmt = getDatabase()->prepare($req);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    /**
     * Met à jour le nom de la ville d'une agence existante.
     * @param int $id l'identifiant de l'agence à mettre à jour.
     * @param string $ville le nouveau nom de la ville.
     * @return void
     */
    public static function updateAgence($id, $ville)
    {
        $db = getDatabase();
        $sql = "UPDATE Agence SET ville = :ville WHERE Id_Agence = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':ville' => $ville, ':id' => $id]);
    }

    /**
     * Supprime une agence de la base de données.
     * Attention : Peut échouer si des contraintes de clé étrangère ne sont pas en CASCADE.
     *
     * @param int $id L'identifiant de l'agence à supprimer.
     * @return void
     */
    public static function deleteAgence($id)
    {
        $db = getDatabase();
        $sql = "DELETE FROM Agence WHERE Id_Agence = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }
}
