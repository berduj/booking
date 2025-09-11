<?php

declare(strict_types=1);

namespace App\Service\Tools\Merge;

use App\Entity\Personne;
use Doctrine\ORM\EntityManagerInterface;

class PersonneMerger
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function merge(Personne $keep, Personne $delete): void
    {
        if ($delete->getEnabled()) {
            throw new \Exception('La personne à supprimer doit être désactivée');
        }

        if (!$keep->getEnabled()) {
            throw new \Exception('La personne à conserver doit être activée');
        }

        if (trim(strtolower((string) $keep->getNom())) !== trim(strtolower((string) $delete->getNom()))) {
            throw new \Exception('Les 2 personnes doivent avoir le même nom');
        }

        if (trim(strtolower((string) $keep->getPrenom())) !== trim(strtolower((string) $delete->getPrenom()))) {
            throw new \Exception('Les 2 personnes doivent avoir le même prénom');
        }

        $keep_id = $keep->getId() ? $keep->getId()->toBinary() : null;
        $delete_id = $delete->getId() ? $delete->getId()->toBinary() : null;

        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();
        try {
            $conn->executeStatement('UPDATE IGNORE action_participant SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM action_participant WHERE personne_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE action_pilotage SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM action_pilotage WHERE personne_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE alerte SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM alerte WHERE personne_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE contact SET auteur_id= :keep_id WHERE auteur_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM contact WHERE auteur_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE contact_interlocuteur SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM contact_interlocuteur WHERE personne_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE personne_service SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM personne_service WHERE personne_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE personne_territoire SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM personne_territoire WHERE personne_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE personne_thematique_relation SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM personne_thematique_relation WHERE personne_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE personne_widget SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM personne_widget WHERE personne_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('DELETE FROM reset_password_request WHERE user_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE security_log SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE structure_personne SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_personne WHERE personne_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('UPDATE IGNORE structure_referent SET personne_id= :keep_id WHERE personne_id = :delete_id', ['keep_id' => $keep_id, 'delete_id' => $delete_id]);
            $conn->executeStatement('DELETE FROM structure_referent WHERE personne_id = :delete_id', ['delete_id' => $delete_id]);

            $conn->executeStatement('DELETE FROM personne WHERE id = :delete_id', ['delete_id' => $delete_id]);
            $conn->commit();

            if ($delete->getUsername() and !$keep->getUsername()) {
                $keep->setUsername($delete->getUsername());
                $keep->setPassword($delete->getPassword());
                $keep->setProfil($delete->getProfil());
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
