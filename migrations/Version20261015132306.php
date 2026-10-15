<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261015132306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, title, status, date, is_validated FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, max_places INTEGER NOT NULL, date DATETIME NOT NULL, is_validated BOOLEAN NOT NULL, information CLOB NOT NULL, category VARCHAR(50) NOT NULL, image VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO event (id, title, max_places, date, is_validated) SELECT id, title, status, date, is_validated FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, is_validated, title, date, max_places FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, is_validated BOOLEAN NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL, status INTEGER NOT NULL)');
        $this->addSql('INSERT INTO event (id, is_validated, title, date, status) SELECT id, is_validated, title, date, max_places FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }
}
