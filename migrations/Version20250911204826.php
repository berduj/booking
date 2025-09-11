<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250911204826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE personne_tag (personne_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', tag_id INT NOT NULL, INDEX IDX_9B46B589A21BD112 (personne_id), INDEX IDX_9B46B589BAD26311 (tag_id), PRIMARY KEY(personne_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE personne_tag ADD CONSTRAINT FK_9B46B589A21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personne_tag ADD CONSTRAINT FK_9B46B589BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personne_tag DROP FOREIGN KEY FK_9B46B589A21BD112');
        $this->addSql('ALTER TABLE personne_tag DROP FOREIGN KEY FK_9B46B589BAD26311');
        $this->addSql('DROP TABLE personne_tag');
    }
}
