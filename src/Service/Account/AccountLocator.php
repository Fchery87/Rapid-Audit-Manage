<?php

namespace App\Service\Account;

use App\Security\SensitiveDataProtector;
use Doctrine\DBAL\Connection;

class AccountLocator
{
    public function __construct(
        private readonly Connection $connection,
        private readonly SensitiveDataProtector $protector,
    ) {
    }

    public function findAccountByFile(string $filename): ?array
    {
        $sql = 'SELECT a.aid, a.first_name, a.last_name, a.email, a.phone, a.address1, a.address2, a.city, a.state, a.zip '
            . 'FROM accounts a INNER JOIN account_files f ON f.aid = a.aid WHERE f.filename = :filename';

        $result = $this->connection->executeQuery($sql, ['filename' => $filename]);
        $account = $result->fetchAssociative() ?: null;

        return $account ? $this->protector->decryptAccountRecord($account) : null;
    }

    public function findAccountByAid(int $aid): ?array
    {
        $sql = 'SELECT a.aid, a.first_name, a.last_name, a.email, a.phone, a.address1, a.address2, a.city, a.state, a.zip '
            . 'FROM accounts a WHERE a.aid = :aid';
        $result = $this->connection->executeQuery($sql, ['aid' => $aid]);
        $account = $result->fetchAssociative() ?: null;

        return $account ? $this->protector->decryptAccountRecord($account) : null;
    }
}
