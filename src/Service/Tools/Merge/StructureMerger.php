<?php

declare(strict_types=1);

namespace App\Service\Tools\Merge;

use App\Entity\Structure;
use Doctrine\ORM\EntityManagerInterface;

class StructureMerger
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function merge(Structure $keep, Structure $delete): void
    {
        if ($delete->getEnabled()) {
            throw new \Exception('La structure à supprimer doit être désactivée');
        }

        if (!$keep->getEnabled()) {
            throw new \Exception('La structure à conserver doit être activée');
        }

        if (trim(strtolower((string) $keep->getRaisonSociale())) !== trim(strtolower((string) $delete->getRaisonSociale()))) {
            throw new \Exception('Les 2 structures doivent avoir le même nom');
        }

        if (trim(strtolower((string) $keep->getCodePostal())) !== trim(strtolower((string) $delete->getCodePostal()))) {
            throw new \Exception('Les 2 structures doivent avoir le même code postal');
        }

        $keep_id = $keep->getId() ? $keep->getId()->toBinary() : null;
        $delete_id = $delete->getId() ? $delete->getId()->toBinary() : null;

        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();
        try {
            // deplacer les actions
            $conn->executeStatement('UPDATE IGNORE action_participant SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM action_participant WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE action_pilotage SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM action_pilotage WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE contact_interlocuteur SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM contact_interlocuteur WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE structure_intention_partenariat SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_intention_partenariat WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE structure_personne SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_personne WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE structure_qpv SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_qpv WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE structure_referent SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_referent WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE structure_tag SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_tag WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE structure_territoire SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_territoire WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE structure_thematique_relation SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_thematique_relation WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE structure_type_structure SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_type_structure WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            // deplacer les filieres
            //            $conn->executeStatement('UPDATE IGNORE structure_filiere SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            //            $conn->executeStatement('DELETE FROM structure_filiere WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            // deplacer les territoires
            $conn->executeStatement('UPDATE IGNORE structure_territoire SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_territoire WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            // deplacer les type structure
            $conn->executeStatement('UPDATE IGNORE structure_type_structure SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_type_structure WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            // deplacer les notes
            $conn->executeStatement('UPDATE IGNORE contact_interlocuteur SET structure_id= :keep_id WHERE structure_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM contact_interlocuteur WHERE structure_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('DELETE FROM structure WHERE id = :delete_id', ['delete_id' => $delete_id]);
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
