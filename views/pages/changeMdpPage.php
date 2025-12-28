/**
* Vue pour la page de changement de mot de passe lors de la première connexion.
*/


<!-- Contenu spécifique à la page de chngement de mot de passe-->
h2>Première connexion</h2>
<p>Pour des raisons de sécurité, vous devez changer votre mot de passe avant de continuer.</p>

<form action="index.php?page=updateMdpAction" method="POST">
    <!-- Champ caché pour la protection CSRF -->
    <input type="hidden" name="csrf_token" value="<?= \generateCSRFToken() ?>">
    <input type="password" name="new_mdp" placeholder="Nouveau mot de passe" required>
    <input type="password" name="confirm_mdp" placeholder="Confirmez le mot de passe" required>
    <button type="submit">Enregistrer</button>
</form>