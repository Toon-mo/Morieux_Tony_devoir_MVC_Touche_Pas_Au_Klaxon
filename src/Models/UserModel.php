<?php

/**
 * Fichier contenant toutes les fonctions liées à la table "Utilisateur" (Modèle).
 * Fournit les opérations de lecture et d'écriture (CRUD) pour les utilisateurs.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Models
 * @author Tony Morieux
 */

namespace Morieuxtony\MvcTest\Models;

use PDO;

use function Morieuxtony\MvcTest\Config\getDatabase;

class UserModel
{
    /**
     * Récupère tous les utilisateurs de la base de données.
     *
     * @return array Une liste de tous les utilisateurs.
     */
    public static function getAllUsers()
    {
        $req = "SELECT * FROM Utilisateur";
        $stmt = getDatabase()->prepare($req);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $datas;
    }

    /**
     * Récupère un utilisateur spécifique par son identifiant.
     *
     * @param int $id L'identifiant unique de l'utilisateur.
     * @return array|false Le tableau associatif de l'utilisateur si trouvé, sinon false.
     */
    public static function getUserById($id)
    {
        $req = "SELECT * FROM Utilisateur WHERE Id_Utilisateur = :id";
        $stmt = getDatabase()->prepare($req);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    /**
     * Récupère un utilisateur par son nom de famille.
     *
     * @param string $username Le nom de famille à rechercher.
     * @return array|false Les données de l'utilisateur si trouvé, sinon false.
     */
    public static function getUserByUsername($username)
    {
        $req = "SELECT * FROM Utilisateur WHERE nom_utilisateur = :username";
        $stmt = getDatabase()->prepare($req);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    /**
     * Récupère un utilisateur par son prénom.
     *
     * @param string $prenom Le prénom à rechercher.
     * @return array|false Les données de l'utilisateur si trouvé, sinon false.
     */
    public static function getUserByPrenom($prenom)
    {
        $req = "SELECT * FROM Utilisateur WHERE prenom_utilisateur = :prenom";
        $stmt = getDatabase()->prepare($req);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    /**
     * Récupère un utilisateur par son adresse email.
     *
     * @param string $email L'adresse email à rechercher.
     * @return array|false Les données de l'utilisateur si trouvé, sinon false.
     */
    public static function getUserByEmail($email)
    {
        $req = "SELECT * FROM Utilisateur WHERE email = :email";
        $stmt = getDatabase()->prepare($req);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    /**
     * Vérifie les identifiants de l'utilisateur et initialise la session si la connexion est réussie.
     *
     * @param string $email    L'email fourni par l'utilisateur.
     * @param string $password Le mot de passe en clair fourni par l'utilisateur.
     * @return bool             True si la connexion est réussie, false sinon.
     */
    public static function loginUser($email, $password)
    {
        $pdo = getDatabase();
        $sql = "SELECT * FROM Utilisateur WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user'] = [
                'id' => $user['Id_Utilisateur'],
                'email' => $user['email'],
                'userLastname' => $user['nom_utilisateur'],
                'userFirstname' => $user['prenom_utilisateur'],
                'telephone' => $user['telephone'],
                'admin' => $user['admin'],
                'Id_Agence' => $user['Id_Agence']
            ];
            return true;
        }

        return false;
    }

    /**
     * Crée un nouvel utilisateur dans la base de données. (Admin)
     *
     * @param string $nom       Le nom de famille de l'utilisateur.
     * @param string $prenom    Le prénom de l'utilisateur.
     * @param string $email     L'email de l'utilisateur.
     * @param string $tel       Le numéro de téléphone.
     * @param string $mdpHash   Le mot de passe déjà haché.
     * @param int    $isAdmin   Le statut administrateur (1 pour oui, 0 pour non).
     * @param int    $idAgence  L'ID de l'agence de rattachement.
     * @return void
     */
    public static function createUser($nom, $prenom, $email, $tel, $mdpHash, $isAdmin, $idAgence)
    {
        $db = getDatabase();
        $sql = "INSERT INTO Utilisateur (nom_utilisateur,
        prenom_utilisateur,
        email, telephone,
        mot_de_passe,
        admin,
        Id_Agence) 
            VALUES (:nom, :prenom, :email, :tel, :mdp, :admin, :agence)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':tel' => $tel,
            ':mdp' => $mdpHash,
            ':admin' => $isAdmin,
            ':agence' => $idAgence
        ]);
    }

    /**
     * Met à jour les informations d'un utilisateur existant. (Admin)
     * Ne met pas à jour le mot de passe.
     *
     * @param int    $id        L'ID de l'utilisateur à modifier.
     * @param string $nom       Le nouveau nom de famille.
     * @param string $prenom    Le nouveau prénom.
     * @param string $email     Le nouvel email.
     * @param string $tel       Le nouveau téléphone.
     * @param int    $isAdmin   Le nouveau statut administrateur.
     * @param int    $idAgence  Le nouvel ID de l'agence.
     * @return void
     */
    public static function updateUser($id, $nom, $prenom, $email, $tel, $isAdmin, $idAgence)
    {
        $db = getDatabase();
        $sql = "UPDATE Utilisateur SET 
            nom_utilisateur = :nom,
            prenom_utilisateur = :prenom,
            email = :email,
            telephone = :tel,
            admin = :admin,
            Id_Agence = :agence
            WHERE Id_Utilisateur = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':tel' => $tel,
            ':admin' => $isAdmin,
            ':agence' => $idAgence,
            ':id' => $id
        ]);
    }

    /**
     * Supprime un utilisateur de la base de données. (Admin)
     *
     * @param int $id L'identifiant de l'utilisateur à supprimer.
     * @return void
     */
    public static function deleteUser($id)
    {
        $db = getDatabase();
        $sql = "DELETE FROM Utilisateur WHERE Id_Utilisateur = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
