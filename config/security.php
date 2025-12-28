<?php

/**
 * Fonctions de sécurité et d'authentification pour l'application TouchePasAuKlaxon.
 * Gère le CSRF, l'état de connexion, les droits d'administration et les fonctions d'échappement/validation.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Config
 * @author Tony Morieux
 */


/**
 * Génère un jeton CSRF (Cross-Site Request Forgery) et le stocke en session.
 * Doit être appelé avant la soumission d'un formulaire pour générer le token.
 * Le jeton est stocké dans $_SESSION['csrf_token'].
 * @return string Le jeton CSRF généré.
 */
function generateCSRFToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            // Fallback pour les environnements plus anciens ou en cas d'erreur
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
            error_log("CSRF Token generation fallback: " . $e->getMessage());
        }
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie la validité d'un jeton CSRF soumis par un formulaire.
 *
 * @param string $token Le jeton CSRF reçu du formulaire via $_POST.
 * @return bool True si le jeton est valide (correspond à celui en session), False sinon.
 */
function verifyCSRFToken(string $token): bool
{
    // Vérifie si le jeton de session existe ET s'il correspond au jeton fourni
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        // IMPORTANT: Supprime le jeton de la session après une vérification réussie.
        // Cela garantit que le jeton est à usage unique et ne peut pas être réutilisé.
        unset($_SESSION['csrf_token']);
        return true;
    }
    // Si les jetons ne correspondent pas ou si le jeton de session n'est pas défini
    return false;
}

/**
 * Échappe les données pour l'affichage HTML afin de prévenir les attaques XSS.
 * Utilise htmlspecialchars avec des options de sécurité recommandées.
 *
 * @param mixed $data Les données à échapper (peut être string, int, null, etc.).
 * @return string Les données échappées, ou une chaîne vide si la donnée est null.
 */
function escape($data): string
{
    if ($data === null) {
        return '';
    }

    return htmlspecialchars((string)$data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Vérifie si un utilisateur est actuellement connecté.
 * La connexion est confirmée si la session contient un tableau 'user' avec un 'id'.
 *
 * @return bool True si un utilisateur est connecté, False sinon.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user']) && isset($_SESSION['user']['id']);
}

/**
 * Vérifie si l'utilisateur connecté possède les droits d'administrateur.
 * Nécessite que l'utilisateur soit d'abord connecté.
 *
 * @return bool True si l'utilisateur est connecté et a le statut 'admin' égal à 1, False sinon.
 */
function isAdmin(): bool
{
    // Vérifie d'abord la connexion, puis le statut admin
    return isLoggedIn() && isset($_SESSION['user']['admin']) && $_SESSION['user']['admin'] == 1;
}

/**
 * Force la redirection vers la page de connexion si l'utilisateur n'est pas connecté.
 * Arrête l'exécution du script après la redirection.
 *
 * @return void
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header("Location: index.php?page=login");
        exit;
    }
}

/**
 * Force la redirection vers la page d'accueil si l'utilisateur n'est pas administrateur.
 * Nécessite que l'utilisateur soit connecté avant de vérifier les droits admin.
 *
 * @return void
 */
function requireAdmin(): void
{
    requireLogin(); // S'assure que l'utilisateur est connecté d'abord
    if (!isAdmin()) {
        header("Location: index.php?page=home");
        exit;
    }
}

/**
 * Vérifie si l'utilisateur connecté est le propriétaire d'un trajet donné.
 *
 * @param int $trajetId L'ID du trajet à vérifier.
 * @param int $userId   L'ID de l'utilisateur (généralement $_SESSION['user']['id']).
 * @return bool True si l'utilisateur est le propriétaire du trajet, False sinon.
 */
function isTrajetOwner(int $trajetId, int $userId): bool
{

    $trajet = \Morieuxtony\MvcTest\Models\TrajetModel::getTrajetById($trajetId);
    return $trajet && (int)$trajet['Id_Conducteur'] === $userId;
}

/**
 * Vérifie que la date d'arrivée est postérieure à la date de départ.
 *
 * @param string $dateDepart La date et heure de départ.
 * @param string $dateArrivee La date et heure d'arrivée.
 * @return bool True si la date d'arrivée est après la date de départ, False sinon.
 */
function validateDateOrder(string $dateDepart, string $dateArrivee): bool
{
    $timestampDepart = strtotime($dateDepart);
    $timestampArrivee = strtotime($dateArrivee);

    // Vérifie que les deux dates sont valides
    if ($timestampDepart === false || $timestampArrivee === false) {
        return false;
    }

    // Vérifie que l'arrivée est après le départ
    return $timestampArrivee > $timestampDepart;
}

/**
 * Nettoie et valide un entier.
 * Retourne l'entier validé ou null si la validation échoue.
 *
 * @param mixed $value La valeur à nettoyer et valider.
 * @return int|null L'entier validé ou null.
 */
function validateInt($value): ?int
{
    // FILTER_VALIDATE_INT accepte les entiers, ignore les espaces et peut filtrer les plages.
    $filtered = filter_var($value, FILTER_VALIDATE_INT);
    return $filtered !== false ? $filtered : null;
}

/**
 * Valide une adresse email.
 *
 * @param string $email L'adresse email à valider.
 * @return bool True si l'email est valide, False sinon.
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide une date et heure au format ISO 8601 ou tout format compris par strtotime().
 * Vérifie que la date est valide et qu'elle n'est pas dans le passé (pour les futurs trajets).
 *
 * @param string $dateTime La date et heure à valider (ex: "2025-12-31T10:30").
 * @param bool $allowPast Si True, accepte les dates passées (par exemple, pour une date de naissance).
 * @return bool True si la date et l'heure sont valides, False sinon.
 */
function validateDateTime(string $dateTime, bool $allowPast = false): bool
{
    $timestamp = strtotime($dateTime);

    // Si strtotime retourne false, le format est invalide
    if ($timestamp === false) {
        return false;
    }

    if ($timestamp <= 0) {
        return false;
    }

    // Si on ne permet pas les dates passées
    if (!$allowPast && $timestamp < time()) {
        return false; // La date est passé
    }

    return true;
}
