<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250921120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create credit_reports table to persist parsed Simple Audit data';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('credit_reports')) {
            $table = $schema->createTable('credit_reports');
            $table->addColumn('id', 'integer', ['autoincrement' => true]);
            $table->addColumn('account_aid', 'integer', ['notnull' => true]);
            $table->addColumn('filename', 'string', ['length' => 255, 'notnull' => true]);
            $table->addColumn('parsed_at', 'datetime_immutable', ['notnull' => true]);
            $table->addColumn('client_data', 'json', ['notnull' => true]);
            $table->addColumn('derogatory_accounts', 'json', ['notnull' => true]);
            $table->addColumn('inquiry_accounts', 'json', ['notnull' => true]);
            $table->addColumn('public_records', 'json', ['notnull' => true]);
            $table->addColumn('credit_info', 'json', ['notnull' => true]);
            $table->addColumn('meta', 'json', ['notnull' => false]);

            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['filename'], 'uniq_credit_reports_filename');
            $table->addIndex(['account_aid'], 'idx_credit_reports_aid');
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('credit_reports')) {
            $schema->dropTable('credit_reports');
        }
    }
}