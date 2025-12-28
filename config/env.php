<?php

/**
 * Fichier de chargement des variables d'environnement depuis le fichier .env.
 * Les variables sont disponibles via getenv() et $_ENV.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Config
 * @author Tony Morieux
 */

$envFile = __DIR__ . '/../.env'; // Le .env est à la racine du projet

// Vérifie si le fichier .env existe
if (!file_exists($envFile)) {
    die("ERREUR FATALE : Le fichier .env est introuvable à : " . $envFile . "<br>" .
        "Veuillez copier le fichier .env.example vers .env et configurer vos identifiants.");
}

// Lit le fichier .env ligne par ligne
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Parcourt chaque ligne pour charger les variables
foreach ($lines as $line) {
    $line = trim($line); // Nettoie les espaces blancs au début et à la fin

    // Ignorer les lignes de commentaires ou les lignes vides
    if (empty($line) || strpos($line, '#') === 0) {
        continue;
    }

    // Sépare le nom de la variable de sa valeur
    // Gère les valeurs contenant des '=' en limitant l'explosion à 2 parties
    if (strpos($line, '=') !== false) {
        list($name, $value) = explode('=', $line, 2); // Le '2' limite l'explosion aux 2 premiers '='
        $name = trim($name);
        $value = trim($value);

        // Définit la variable d'environnement pour getenv() et $_ENV
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
}
