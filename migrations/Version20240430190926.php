<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240430190926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation_add DROP FOREIGN KEY FK_4A4A16936BBD148');
        $this->addSql('ALTER TABLE formation_add_categorie DROP FOREIGN KEY FK_A9818B2A1385DE8A');
        $this->addSql('ALTER TABLE formation_add_categorie DROP FOREIGN KEY FK_A9818B2ABCF5E72D');
        $this->addSql('DROP TABLE formation_add');
        $this->addSql('DROP TABLE formation_add_categorie');
        $this->addSql('ALTER TABLE user ADD is_verified TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formation_add (id INT AUTO_INCREMENT NOT NULL, playlist_id INT DEFAULT NULL, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date DATE NOT NULL, INDEX IDX_4A4A16936BBD148 (playlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE formation_add_categorie (formation_add_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_A9818B2A1385DE8A (formation_add_id), INDEX IDX_A9818B2ABCF5E72D (categorie_id), PRIMARY KEY(formation_add_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE formation_add ADD CONSTRAINT FK_4A4A16936BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)');
        $this->addSql('ALTER TABLE formation_add_categorie ADD CONSTRAINT FK_A9818B2A1385DE8A FOREIGN KEY (formation_add_id) REFERENCES formation_add (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_add_categorie ADD CONSTRAINT FK_A9818B2ABCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP is_verified');
    }
}
