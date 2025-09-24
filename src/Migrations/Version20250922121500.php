<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250922121500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create dispute workflow tables for cases, tasks, letters, and collaboration notes';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('dispute_cases')) {
            $caseTable = $schema->createTable('dispute_cases');
            $caseTable->addColumn('id', 'integer', ['autoincrement' => true]);
            $caseTable->addColumn('account_aid', 'integer', ['notnull' => true]);
            $caseTable->addColumn('title', 'string', ['length' => 180, 'notnull' => true]);
            $caseTable->addColumn('status', 'string', ['length' => 40, 'notnull' => true]);
            $caseTable->addColumn('summary', 'text', ['notnull' => false]);
            $caseTable->addColumn('target_items', 'json', ['notnull' => true]);
            $caseTable->addColumn('created_at', 'datetime_immutable', ['notnull' => true]);
            $caseTable->addColumn('updated_at', 'datetime_immutable', ['notnull' => true]);

            $caseTable->setPrimaryKey(['id']);
            $caseTable->addIndex(['account_aid'], 'idx_dispute_cases_account');
        }

        if (!$schema->hasTable('dispute_tasks')) {
            $taskTable = $schema->createTable('dispute_tasks');
            $taskTable->addColumn('id', 'integer', ['autoincrement' => true]);
            $taskTable->addColumn('dispute_case_id', 'integer', ['notnull' => true]);
            $taskTable->addColumn('description', 'string', ['length' => 255, 'notnull' => true]);
            $taskTable->addColumn('assigned_to', 'string', ['length' => 60, 'notnull' => true]);
            $taskTable->addColumn('client_visible', 'boolean', ['notnull' => true]);
            $taskTable->addColumn('status', 'string', ['length' => 32, 'notnull' => true]);
            $taskTable->addColumn('due_at', 'datetime', ['notnull' => false]);
            $taskTable->addColumn('created_at', 'datetime_immutable', ['notnull' => true]);
            $taskTable->addColumn('completed_at', 'datetime', ['notnull' => false]);
            $taskTable->addColumn('created_by', 'string', ['length' => 120, 'notnull' => true]);

            $taskTable->setPrimaryKey(['id']);
            $taskTable->addIndex(['dispute_case_id'], 'idx_dispute_tasks_case');
            $taskTable->addIndex(['status'], 'idx_dispute_tasks_status');
            $taskTable->addForeignKeyConstraint('dispute_cases', ['dispute_case_id'], ['id'], ['onDelete' => 'CASCADE'], 'fk_dispute_tasks_case');
        }

        if (!$schema->hasTable('dispute_letters')) {
            $letterTable = $schema->createTable('dispute_letters');
            $letterTable->addColumn('id', 'integer', ['autoincrement' => true]);
            $letterTable->addColumn('dispute_case_id', 'integer', ['notnull' => true]);
            $letterTable->addColumn('bureau', 'string', ['length' => 120, 'notnull' => true]);
            $letterTable->addColumn('prepared_by', 'string', ['length' => 120, 'notnull' => true]);
            $letterTable->addColumn('status', 'string', ['length' => 32, 'notnull' => true]);
            $letterTable->addColumn('body', 'text', ['notnull' => true]);
            $letterTable->addColumn('items', 'json', ['notnull' => true]);
            $letterTable->addColumn('created_at', 'datetime_immutable', ['notnull' => true]);
            $letterTable->addColumn('sent_at', 'datetime', ['notnull' => false]);

            $letterTable->setPrimaryKey(['id']);
            $letterTable->addIndex(['dispute_case_id'], 'idx_dispute_letters_case');
            $letterTable->addIndex(['status'], 'idx_dispute_letters_status');
            $letterTable->addForeignKeyConstraint('dispute_cases', ['dispute_case_id'], ['id'], ['onDelete' => 'CASCADE'], 'fk_dispute_letters_case');
        }

        if (!$schema->hasTable('collaboration_notes')) {
            $noteTable = $schema->createTable('collaboration_notes');
            $noteTable->addColumn('id', 'integer', ['autoincrement' => true]);
            $noteTable->addColumn('dispute_case_id', 'integer', ['notnull' => true]);
            $noteTable->addColumn('author', 'string', ['length' => 120, 'notnull' => true]);
            $noteTable->addColumn('message', 'text', ['notnull' => true]);
            $noteTable->addColumn('visibility', 'string', ['length' => 32, 'notnull' => true]);
            $noteTable->addColumn('created_at', 'datetime_immutable', ['notnull' => true]);

            $noteTable->setPrimaryKey(['id']);
            $noteTable->addIndex(['dispute_case_id'], 'idx_collaboration_notes_case');
            $noteTable->addForeignKeyConstraint('dispute_cases', ['dispute_case_id'], ['id'], ['onDelete' => 'CASCADE'], 'fk_collaboration_notes_case');
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('collaboration_notes')) {
            $schema->dropTable('collaboration_notes');
        }

        if ($schema->hasTable('dispute_letters')) {
            $schema->dropTable('dispute_letters');
        }

        if ($schema->hasTable('dispute_tasks')) {
            $schema->dropTable('dispute_tasks');
        }

        if ($schema->hasTable('dispute_cases')) {
            $schema->dropTable('dispute_cases');
        }
    }
}
