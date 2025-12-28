<?php

require_once __DIR__ . '/config/env.php';

/**
 * @file seed.php
 * Script de remplissage initial (seeding) de la base de données.
 *
 * Ce script est conçu pour être exécuté manuellement afin d'initialiser la base de données
 * avec des données fictives (Agences, Utilisateurs, Trajets) pour les tests et le développement.
 * Il vide préalablement les tables concernées pour garantir un état propre à chaque exécution.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Scripts
 * @author Tony Morieux
 *
 * @uses env.php Pour charger les variables d'environnement de connexion à la BDD.
 * @uses PDO Pour interagir avec la base de données MySQL.
 * @var string $host Hôte de la base de données.
 * @var string $dbname Nom de la base de données.
 * @var string $username Nom d'utilisateur de la base de données.
 * @var string $password Mot de passe de la base de données.
 * @var PDO $pdo L'objet de connexion à la base de données.
 *
 * Dépendances de fichiers externes :
 * - agences.txt : Fichier contenant une ville par ligne pour peupler la table Agence.
 * - users.txt : Fichier contenant les informations des utilisateurs (nom,prenom,telephone,email) séparées par des virgules.
 */

// Configuration de la base de données (chargée depuis env.php)
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Configuration pour lever des exceptions en cas d'erreur SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion à la base de données réussie.<br>";

    // ---------------------------------------------------------
    // 1. NETTOYAGE (On vide les tables pour éviter les doublons)
    // ---------------------------------------------------------

    // Désactivation temporaire des contraintes de clés étrangères pour un TRUNCATE propre
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE Trajet");
    $pdo->exec("TRUNCATE TABLE Utilisateur");
    $pdo->exec("TRUNCATE TABLE Agence");
    // Réactivation des contraintes de clés étrangères
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Tables vidées.<br>";

    // ---------------------------------------------------------
    // 2. IMPORT DES AGENCES
    // ---------------------------------------------------------
    $fichierAgences = __DIR__ . '/public/assets/jeu-d-essais/agences.txt';
    if (!file_exists($fichierAgences)) {
        die("Erreur : Le fichier $fichierAgences est introuvable.");
    }

    // Lecture du fichier, une ville par ligne
    $agences = file($fichierAgences, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $stmtAgence = $pdo->prepare("INSERT INTO Agence (ville) VALUES (:ville)");

    // On stocke les IDs des agences insérées pour les utiliser plus tard
    $agenceIds = [];

    foreach ($agences as $ville) {
        $stmtAgence->execute([':ville' => trim($ville)]);
        // Récupère l'ID de la dernière ligne insérée
        $agenceIds[] = $pdo->lastInsertId();
    }
    echo count($agences) . " agences importées.<br>";

    // ---------------------------------------------------------
    // 3. IMPORT DES UTILISATEURS
    // ---------------------------------------------------------
    $fichierUsers = __DIR__ . '/public/assets/jeu-d-essais/users.txt';
    if (!file_exists($fichierUsers)) {
        die("Erreur : Le fichier $fichierUsers est introuvable.");
    }

    // Hachage du mot de passe par défaut
    $mdpHash = password_hash('password123', PASSWORD_DEFAULT);

    $stmtUser = $pdo->prepare("
        INSERT INTO Utilisateur (nom_utilisateur, prenom_utilisateur, telephone, email, mot_de_passe, admin, Id_Agence) 
        VALUES (:nom, :prenom, :tel, :email, :mdp, :admin, :id_agence)
    ");

    $lignesUsers = file($fichierUsers, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $compteurUsers = 0;

    foreach ($lignesUsers as $ligne) {
        // Format attendu par ligne: nom,prenom,telephone,email
        $data = explode(',', $ligne);

        if (count($data) == 4) {
            $nom = trim($data[0]);
            $prenom = trim($data[1]);
            $tel = trim($data[2]);
            $email = trim($data[3]);

            // Attribution aléatoire d'une agence parmi celles importées
            $idAgenceAleatoire = $agenceIds[array_rand($agenceIds)];

            // Le tout premier utilisateur créé est défini comme administrateur
            $isAdmin = ($compteurUsers === 0) ? 1 : 0;

            $stmtUser->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':tel' => $tel,
                ':email' => $email,
                ':mdp' => $mdpHash,
                ':admin' => $isAdmin,
                ':id_agence' => $idAgenceAleatoire
            ]);
            $compteurUsers++;
        }
    }
    echo "$compteurUsers utilisateurs importés (Le premier est Admin, MDP: 'password123').<br>";

    // ---------------------------------------------------------
    // 4. CRÉATION DE TRAJETS FICTIFS (Pour tester l'affichage)
    // ---------------------------------------------------------
    $stmtTrajet = $pdo->prepare("
        INSERT INTO Trajet (date_heure_depart, date_heure_arrivee, nb_places_total, nb_places_dispo, Id_Conducteur, Id_Agence_Depart, Id_Agence_Arrivee)
        VALUES (:dep, :arr, :nb, :nb_dispo, :cond, :ag_dep, :ag_arr)
    ");

    // Récupération des IDs des utilisateurs pour les assigner comme conducteurs
    $userIds = $pdo->query("SELECT Id_Utilisateur FROM Utilisateur")->fetchAll(PDO::FETCH_COLUMN);

    for ($i = 0; $i < 5; $i++) {
        // Sélection aléatoire d'agences de départ et d'arrivée différentes
        $cleDep = array_rand($agenceIds);
        $cleArr = array_rand($agenceIds);
        while ($cleDep === $cleArr) {
            $cleArr = array_rand($agenceIds);
        }

        // Calcul des dates aléatoires pour le futur
        $dateDepart = new DateTime("now + " . ($i + 1) . " day");
        $dateDepart->setTime(rand(6, 18), 0); // Départ entre 6h et 18h

        $dateArrivee = clone $dateDepart;
        $dateArrivee->modify("+ " . rand(1, 5) . " hours"); // Durée entre 1h et 5h

        $nbPlacesTotal = rand(2, 8);

        $stmtTrajet->execute([
            ':dep' => $dateDepart->format('Y-m-d H:i:s'),
            ':arr' => $dateArrivee->format('Y-m-d H:i:s'),
            ':nb' => $nbPlacesTotal,
            ':nb_dispo' => rand(1, $nbPlacesTotal), // Places disponibles aléatoires
            ':cond' => $userIds[array_rand($userIds)], // Conducteur aléatoire
            ':ag_dep' => $agenceIds[$cleDep],
            ':ag_arr' => $agenceIds[$cleArr]
        ]);
    }
    echo "5 trajets fictifs créés pour tester l'affichage.<br>";

    echo "<hr><strong>Terminé avec succès !</strong>";
} catch (PDOException $e) {
    // En cas d'erreur de connexion ou d'exécution SQL
    http_response_code(500);
    die("Erreur SQL lors du seeding : " . $e->getMessage());
}
