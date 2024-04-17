<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240417180038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oeuvres (idoeuvre INT AUTO_INCREMENT NOT NULL, prix INT NOT NULL, titre VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, img VARCHAR(255) NOT NULL, iduser INT NOT NULL, PRIMARY KEY(idoeuvre)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservations (idReservation INT AUTO_INCREMENT NOT NULL, dateReservation DATE NOT NULL, statut VARCHAR(255) NOT NULL, oeuvreID INT DEFAULT NULL, INDEX IDX_4DA239473A9738 (oeuvreID), PRIMARY KEY(idReservation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239473A9738 FOREIGN KEY (oeuvreID) REFERENCES oeuvres (idoeuvre)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239473A9738');
        $this->addSql('DROP TABLE oeuvres');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
