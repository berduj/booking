<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250911221620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artiste (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artiste_personne (artiste_id INT NOT NULL, personne_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_420BF5F621D25844 (artiste_id), INDEX IDX_420BF5F6A21BD112 (personne_id), PRIMARY KEY(artiste_id, personne_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artiste_tag (artiste_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_19CD88C121D25844 (artiste_id), INDEX IDX_19CD88C1BAD26311 (tag_id), PRIMARY KEY(artiste_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artiste_personne ADD CONSTRAINT FK_420BF5F621D25844 FOREIGN KEY (artiste_id) REFERENCES artiste (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artiste_personne ADD CONSTRAINT FK_420BF5F6A21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artiste_tag ADD CONSTRAINT FK_19CD88C121D25844 FOREIGN KEY (artiste_id) REFERENCES artiste (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artiste_tag ADD CONSTRAINT FK_19CD88C1BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artiste_personne DROP FOREIGN KEY FK_420BF5F621D25844');
        $this->addSql('ALTER TABLE artiste_personne DROP FOREIGN KEY FK_420BF5F6A21BD112');
        $this->addSql('ALTER TABLE artiste_tag DROP FOREIGN KEY FK_19CD88C121D25844');
        $this->addSql('ALTER TABLE artiste_tag DROP FOREIGN KEY FK_19CD88C1BAD26311');
        $this->addSql('DROP TABLE artiste');
        $this->addSql('DROP TABLE artiste_personne');
        $this->addSql('DROP TABLE artiste_tag');
    }
}
