<?php

/**
 * Fichier de configuration et de connexion à la base de données.
 * Utilise un fichier .env pour les identifiants et le pattern Singleton pour la connexion PDO.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Config
 * @author Tony Morieux
 */

namespace Morieuxtony\MvcTest\Config;

use PDO;
use PDOException;
use Exception;

// Charge les variables d'environnement (ex: DB_HOST, DB_NAME)
require_once __DIR__ . '/env.php';

// Constantes de configuration pour la base de données
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_CHARSET', 'utf8mb4');

/**
 * Établit et retourne une instance unique de la connexion PDO à la base de données (Singleton).
 * La connexion n'est créée qu'une seule fois par requête pour optimiser les performances.
 *
 * @return PDO       L'objet de connexion PDO.
 * @throws Exception Si la connexion à la base de données échoue.
 */
function getDatabase()
{
    // La variable statique $pdo persiste à travers les appels de la fonction
    static $pdo = null;

    // Si la connexion n'a pas encore été établie
    if ($pdo === null) {
        try {
            // Construction du DSN (Data Source Name)
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            // Options de connexion PDO pour la sécurité et la gestion des erreurs
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lève des exceptions en cas d'erreur SQL
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Récupère les résultats en tableaux associatifs
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise les vraies requêtes préparées du SGBD
            ];

            // Création de l'instance PDO
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En cas d'échec de la connexion, on log l'erreur et on lève une exception générique
            error_log('Erreur de connexion BDD : ' . $e->getMessage());
            throw new Exception('Impossible de se connecter à la base de données. Veuillez contacter un administrateur.');
        }
    }

    // Retourne l'instance de connexion (existante ou nouvellement créée)
    return $pdo;
}
