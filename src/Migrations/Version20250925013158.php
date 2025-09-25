<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250925013158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user table for authentication.';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('user')) {
            return;
        }

        $table = $schema->createTable('user');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('username', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('password', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('email', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('roles', 'json', ['notnull' => true]);
        $table->addColumn('mfa_secret', 'string', ['length' => 255, 'notnull' => false]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['username'], 'UNIQ_8D93D649F85E0677');
        $table->addUniqueIndex(['email'], 'UNIQ_8D93D649E7927C74');
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('user')) {
            $schema->dropTable('user');
        }
    }
}

