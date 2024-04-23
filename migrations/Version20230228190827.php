<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228190827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE curriculum (id INT AUTO_INCREMENT NOT NULL, alumne_id INT DEFAULT NULL, experiencia VARCHAR(255) DEFAULT NULL, idiomes VARCHAR(255) DEFAULT NULL, estudis VARCHAR(255) DEFAULT NULL, competencies VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_7BE2A7C39395058A (alumne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE curriculum ADD CONSTRAINT FK_7BE2A7C39395058A FOREIGN KEY (alumne_id) REFERENCES alumne (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE curriculum DROP FOREIGN KEY FK_7BE2A7C39395058A');
        $this->addSql('DROP TABLE curriculum');
    }
}
