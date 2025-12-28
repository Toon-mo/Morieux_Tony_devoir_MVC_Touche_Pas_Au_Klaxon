<?php

/**
 * @file utilities.php
 * Fichier contenant des fonctions utilitaires globales.
 * Ces fonctions sont utilisées à travers toute l'application.
 * @package TouchePasAuKlaxon
 * @subpackage Controllers
 * @author Tony Morieux
 *
 */


/**
 * Affiche le contenu d'un tableau de manière lisible.
 * Utile pour le débogage.
 * @param array $array le tableau à afficher.
 * @return void
 */
function showArray($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

/**
 * Construit et affiche la page finale.
 * Utilise la constante ROOT pour trouver les fichiers depuis la racine du projet.
 * @param array $datas_page Un tableau associatif contenant les données de la page :
 * - 'view' : le chemin de la vue à inclure (relatif à ROOT).
 * - 'layout' : le chemin du layout à utiliser (relatif à ROOT).
 * - autres clés pour les métadonnées (title, description, etc.).
 * @return void
 */
function drawPage($datas_page)
{
    // Extrait les variables ($view, $title, etc.)
    extract($datas_page);

    ob_start();

    // On determine le chemin racine
    $rootPath = defined('ROOT') ? ROOT : dirname(__DIR__, 2);

    // Inclusion de la vue
    // Construction du chemin complet de la vue
    $viewPath = $rootPath . DIRECTORY_SEPARATOR . $view;

    if (file_exists($viewPath)) {
        require_once($viewPath);
    } else {
        // Affichage d'erreur (utile pour le débogage)
        echo "<div style='background: #f8d7da;
         color: #721c24; padding: 20px;
          border: 1px solid #f5c6cb; margin: 20px;'>";
        echo "<h3> Erreur : Vue introuvable</h3>";
        echo "<p>Le fichier demandé est : <code>$view</code></p>";
        echo "<p>PHP a cherché ici : <code>$viewPath</code></p>";
        echo "</div>";
    }

    $content = ob_get_clean();

    // Inclusion du layout
    $layoutPath = $rootPath . DIRECTORY_SEPARATOR . $layout;

    if (file_exists($layoutPath)) {
        require_once($layoutPath);
    } else {
        // Affichage d'erreur utile pour le débogage
        echo $content;
        echo "<p style='color:red; text-align:center;'> Attention : Layout introuvable ($layoutPath)</p>";
    }
}
