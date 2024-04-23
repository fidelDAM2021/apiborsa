<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502134520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oferta (id INT AUTO_INCREMENT NOT NULL, nifempresa_id VARCHAR(9) NOT NULL, data DATE NOT NULL, estat TINYINT(1) DEFAULT NULL, textoferta LONGTEXT NOT NULL, experiencia VARCHAR(255) DEFAULT NULL, idiomes VARCHAR(255) DEFAULT NULL, altres VARCHAR(255) DEFAULT NULL, urloferta VARCHAR(255) DEFAULT NULL, INDEX IDX_7479C8F2476FEB69 (nifempresa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oferta_cicle (oferta_id INT NOT NULL, cicle_id INT NOT NULL, INDEX IDX_AE575E02FAFBF624 (oferta_id), INDEX IDX_AE575E0262328DAC (cicle_id), PRIMARY KEY(oferta_id, cicle_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oferta ADD CONSTRAINT FK_7479C8F2476FEB69 FOREIGN KEY (nifempresa_id) REFERENCES empresa (nif)');
        $this->addSql('ALTER TABLE oferta_cicle ADD CONSTRAINT FK_AE575E02FAFBF624 FOREIGN KEY (oferta_id) REFERENCES oferta (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oferta_cicle ADD CONSTRAINT FK_AE575E0262328DAC FOREIGN KEY (cicle_id) REFERENCES cicle (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oferta DROP FOREIGN KEY FK_7479C8F2476FEB69');
        $this->addSql('ALTER TABLE oferta_cicle DROP FOREIGN KEY FK_AE575E02FAFBF624');
        $this->addSql('ALTER TABLE oferta_cicle DROP FOREIGN KEY FK_AE575E0262328DAC');
        $this->addSql('DROP TABLE oferta');
        $this->addSql('DROP TABLE oferta_cicle');
    }
}
