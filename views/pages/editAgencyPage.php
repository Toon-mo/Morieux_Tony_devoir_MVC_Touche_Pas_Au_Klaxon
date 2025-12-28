<?php

/**
 * @file editAgencyPage.php
 * Fichier de la page de modification d'une agence.
 *
 * Cette page permet à un administrateur de modifier les informations d'une agence existante.
 *
 * @package TouchePasAuKlaxon
 * @subpackage Views
 * @author Tony Morieux
 *
 * @uses array $agence Un tableau associatif contenant les informations sur l'agence à modifier.
 */
?>

<div class="container mt-5">
    <div class="card shadow w-50 mx-auto">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">Modifier l'agence</h3>
        </div>
        <div class="card-body">
            <form action="index.php?page=updateAgencyAction" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="id_agence" value="<?= $agence['Id_Agence'] ?>">

                <div class="mb-3">
                    <label for="ville" class="form-label fw-bold">Nom de la ville</label>
                    <input type="text" class="form-control" name="ville"
                        value="<?= htmlspecialchars($agence['ville']) ?>" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php?page=agenciesPage" class="btn btn-danger">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>