<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251010121000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create audit_log_entries table for security event tracking.';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('audit_log_entries')) {
            return;
        }

        $table = $schema->createTable('audit_log_entries');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('event_type', 'string', ['length' => 120, 'notnull' => true]);
        $table->addColumn('actor_type', 'string', ['length' => 60, 'notnull' => true]);
        $table->addColumn('actor_id', 'string', ['length' => 191, 'notnull' => true]);
        $table->addColumn('account_aid', 'integer', ['notnull' => false]);
        $table->addColumn('subject_type', 'string', ['length' => 120, 'notnull' => false]);
        $table->addColumn('subject_id', 'string', ['length' => 191, 'notnull' => false]);
        $table->addColumn('ip_address', 'string', ['length' => 45, 'notnull' => false]);
        $table->addColumn('user_agent', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('metadata', 'json', ['notnull' => true]);
        $table->addColumn('occurred_at', 'datetime_immutable', ['notnull' => true]);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['event_type'], 'idx_audit_log_event');
        $table->addIndex(['actor_id'], 'idx_audit_log_actor');
        $table->addIndex(['account_aid'], 'idx_audit_log_account');
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('audit_log_entries')) {
            $schema->dropTable('audit_log_entries');
        }
    }
}
