<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261015134605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD COLUMN information CLOB NOT NULL');
        $this->addSql('ALTER TABLE event ADD COLUMN category VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE event ADD COLUMN max_places INTEGER NOT NULL');
        $this->addSql('ALTER TABLE event ADD COLUMN image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, is_validated, title, date FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, is_validated BOOLEAN NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL)');
        $this->addSql('INSERT INTO event (id, is_validated, title, date) SELECT id, is_validated, title, date FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }
}
