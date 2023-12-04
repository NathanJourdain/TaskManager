<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231119180503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, assigned_to_id, title, recurrence, enable, last_completed_at FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, assigned_to_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, recurrence VARCHAR(255) DEFAULT NULL --(DC2Type:dateinterval)
        , enable BOOLEAN NOT NULL, last_completed_at DATETIME DEFAULT NULL, CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, assigned_to_id, title, recurrence, enable, last_completed_at) SELECT id, assigned_to_id, title, recurrence, enable, last_completed_at FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25F4BD7827 ON task (assigned_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, assigned_to_id, title, enable, last_completed_at, recurrence FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, assigned_to_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, enable BOOLEAN NOT NULL, last_completed_at DATETIME DEFAULT NULL, recurrence INTEGER DEFAULT NULL, CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, assigned_to_id, title, enable, last_completed_at, recurrence) SELECT id, assigned_to_id, title, enable, last_completed_at, recurrence FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25F4BD7827 ON task (assigned_to_id)');
    }
}
