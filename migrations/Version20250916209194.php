<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916209194 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE structure DROP IF EXISTS new_lat,  DROP IF EXISTS new_lon;');
        $this->addSql('ALTER TABLE structure ADD new_lat DOUBLE NULL AFTER score_geocode, ADD new_lon DOUBLE NULL AFTER new_lat;');
        $this->addSql('UPDATE structure SET new_lon=latitude, new_lat=longitude;');
     //   $this->addSql('UPDATE structure SET longitude=new_lon, latitude= new_lat;');
        $this->addSql('ALTER TABLE structure DROP new_lat,  DROP new_lon;');


        $this->addSql('ALTER TABLE artiste DROP IF EXISTS new_lat,  DROP IF EXISTS new_lon;');
        $this->addSql('ALTER TABLE artiste ADD new_lat DOUBLE NULL AFTER score_geocode, ADD new_lon DOUBLE NULL AFTER new_lat;');
        $this->addSql('UPDATE artiste SET new_lon=latitude, new_lat=longitude;');
        $this->addSql('UPDATE artiste SET longitude=new_lon, latitude= new_lat;');
        $this->addSql('ALTER TABLE artiste DROP new_lat,  DROP new_lon;');


    }

    public function down(Schema $schema): void
    {

    }
}
