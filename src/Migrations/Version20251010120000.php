<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251010120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add client document storage table and task acknowledgement columns.';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('client_documents')) {
            $table = $schema->createTable('client_documents');
            $table->addColumn('id', 'integer', ['autoincrement' => true]);
            $table->addColumn('account_aid', 'integer', ['notnull' => true]);
            $table->addColumn('stored_name', 'string', ['length' => 128, 'notnull' => true]);
            $table->addColumn('original_name', 'string', ['length' => 255, 'notnull' => true]);
            $table->addColumn('mime_type', 'string', ['length' => 120, 'notnull' => true]);
            $table->addColumn('size', 'integer', ['notnull' => true]);
            $table->addColumn('uploaded_by', 'string', ['length' => 120, 'notnull' => true]);
            $table->addColumn('uploaded_at', 'datetime_immutable', ['notnull' => true]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['account_aid'], 'idx_client_documents_account');
        }

        if ($schema->hasTable('dispute_tasks')) {
            $taskTable = $schema->getTable('dispute_tasks');
            if (!$taskTable->hasColumn('client_acknowledged_at')) {
                $taskTable->addColumn('client_acknowledged_at', 'datetime', ['notnull' => false]);
            }
            if (!$taskTable->hasColumn('client_acknowledged_by')) {
                $taskTable->addColumn('client_acknowledged_by', 'string', ['length' => 120, 'notnull' => false]);
            }
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('client_documents')) {
            $schema->dropTable('client_documents');
        }

        if ($schema->hasTable('dispute_tasks')) {
            $taskTable = $schema->getTable('dispute_tasks');
            if ($taskTable->hasColumn('client_acknowledged_at')) {
                $taskTable->dropColumn('client_acknowledged_at');
            }
            if ($taskTable->hasColumn('client_acknowledged_by')) {
                $taskTable->dropColumn('client_acknowledged_by');
            }
        }
    }
}
