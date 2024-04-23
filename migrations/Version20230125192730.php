<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230125192730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cicle (id INT AUTO_INCREMENT NOT NULL, nomcicle VARCHAR(100) NOT NULL, graucicle VARCHAR(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contacte (id INT AUTO_INCREMENT NOT NULL, nifempresa_id VARCHAR(9) NOT NULL, nomcontacte VARCHAR(50) NOT NULL, carrec VARCHAR(30) DEFAULT NULL, telefon VARCHAR(30) NOT NULL, email VARCHAR(60) NOT NULL, INDEX IDX_C794A022476FEB69 (nifempresa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE empresa (nif VARCHAR(9) NOT NULL, idsector_id INT NOT NULL, nom VARCHAR(50) NOT NULL, domicili VARCHAR(50) NOT NULL, cpostal VARCHAR(5) NOT NULL, poblacio VARCHAR(50) NOT NULL, telefon VARCHAR(30) NOT NULL, email VARCHAR(60) NOT NULL, password VARCHAR(9) DEFAULT NULL, INDEX IDX_B8D75A50A08E6192 (idsector_id), PRIMARY KEY(nif)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sector (id INT AUTO_INCREMENT NOT NULL, nomsector VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contacte ADD CONSTRAINT FK_C794A022476FEB69 FOREIGN KEY (nifempresa_id) REFERENCES empresa (NIF)');
        $this->addSql('ALTER TABLE empresa ADD CONSTRAINT FK_B8D75A50A08E6192 FOREIGN KEY (idsector_id) REFERENCES sector (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contacte DROP FOREIGN KEY FK_C794A022476FEB69');
        $this->addSql('ALTER TABLE empresa DROP FOREIGN KEY FK_B8D75A50A08E6192');
        $this->addSql('DROP TABLE cicle');
        $this->addSql('DROP TABLE contacte');
        $this->addSql('DROP TABLE empresa');
        $this->addSql('DROP TABLE sector');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
