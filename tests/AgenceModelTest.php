<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Morieuxtony\MvcTest\Models\AgenceModel;

class AgenceModelTest extends TestCase
{
    // Ce test fait tout le cycle : Création, Lecture, Suppression
    public function testAgenceLifecycle()
    {
        // 1. Préparation
        $nomVille = "VilleTest_" . uniqid();

        // 2. CRÉATION
        AgenceModel::createAgence($nomVille);

        // 3. VÉRIFICATION (Lecture)
        // On cherche l'agence qu'on vient de créer
        $agencies = AgenceModel::getAgencies();
        $monAgence = null;

        foreach ($agencies as $a) {
            if ($a['ville'] === $nomVille) {
                $monAgence = $a;
                break;
            }
        }

        // Assertions : Est-ce qu'on l'a trouvée ?
        $this->assertNotNull($monAgence, "L'agence créée devrait exister en BDD.");
        $this->assertEquals($nomVille, $monAgence['ville']);

        // 4. SUPPRESSION
        $id = $monAgence['Id_Agence'];
        AgenceModel::deleteAgence($id);

        // 5. VÉRIFICATION FINALE (Elle ne doit plus exister)
        $agenceApresSuppression = AgenceModel::getAgencyById($id);
        $this->assertFalse($agenceApresSuppression, "L'agence devrait avoir été supprimée.");
    }
}
