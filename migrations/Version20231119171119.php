<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231119171119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE work_session_task (work_session_id INTEGER NOT NULL, task_id INTEGER NOT NULL, PRIMARY KEY(work_session_id, task_id), CONSTRAINT FK_967D89E57A5C410C FOREIGN KEY (work_session_id) REFERENCES work_session (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_967D89E58DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_967D89E57A5C410C ON work_session_task (work_session_id)');
        $this->addSql('CREATE INDEX IDX_967D89E58DB60186 ON work_session_task (task_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, assigned_to_id, title, recurrence FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, assigned_to_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, recurrence INTEGER DEFAULT NULL, CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, assigned_to_id, title, recurrence) SELECT id, assigned_to_id, title, recurrence FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25F4BD7827 ON task (assigned_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE work_session_task');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, assigned_to_id, title, recurrence FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, assigned_to_id INTEGER DEFAULT NULL, work_session_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, recurrence INTEGER DEFAULT NULL, completed BOOLEAN NOT NULL, completed_at DATETIME DEFAULT NULL, CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB257A5C410C FOREIGN KEY (work_session_id) REFERENCES work_session (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, assigned_to_id, title, recurrence) SELECT id, assigned_to_id, title, recurrence FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25F4BD7827 ON task (assigned_to_id)');
        $this->addSql('CREATE INDEX IDX_527EDB257A5C410C ON task (work_session_id)');
    }
}
