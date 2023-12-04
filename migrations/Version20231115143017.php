<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231115143017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__recurring_task AS SELECT id, assigned_to_id, title, last_complete, recurrence FROM recurring_task');
        $this->addSql('DROP TABLE recurring_task');
        $this->addSql('CREATE TABLE recurring_task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, assigned_to_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, last_completed_at DATETIME DEFAULT NULL, recurrence INTEGER NOT NULL, CONSTRAINT FK_FD937634F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO recurring_task (id, assigned_to_id, title, last_completed_at, recurrence) SELECT id, assigned_to_id, title, last_complete, recurrence FROM __temp__recurring_task');
        $this->addSql('DROP TABLE __temp__recurring_task');
        $this->addSql('CREATE INDEX IDX_FD937634F4BD7827 ON recurring_task (assigned_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__recurring_task AS SELECT id, assigned_to_id, title, recurrence, last_completed_at FROM recurring_task');
        $this->addSql('DROP TABLE recurring_task');
        $this->addSql('CREATE TABLE recurring_task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, assigned_to_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, recurrence INTEGER NOT NULL, last_complete DATETIME DEFAULT NULL, CONSTRAINT FK_FD937634F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO recurring_task (id, assigned_to_id, title, recurrence, last_complete) SELECT id, assigned_to_id, title, recurrence, last_completed_at FROM __temp__recurring_task');
        $this->addSql('DROP TABLE __temp__recurring_task');
        $this->addSql('CREATE INDEX IDX_FD937634F4BD7827 ON recurring_task (assigned_to_id)');
    }
}
