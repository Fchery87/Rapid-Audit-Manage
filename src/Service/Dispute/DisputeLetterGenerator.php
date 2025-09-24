<?php

namespace App\Service\Dispute;

use App\Entity\DisputeCase;
use Twig\Environment;

class DisputeLetterGenerator
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @param array<string, mixed> $account
     * @param array<string, mixed> $clientProfile
     */
    public function generateLetter(DisputeCase $case, array $items, array $account, array $clientProfile, string $bureau, string $preparedBy): string
    {
        return $this->twig->render('disputes/letter.txt.twig', [
            'case' => $case,
            'items' => $items,
            'account' => $account,
            'client' => $clientProfile,
            'bureau' => $bureau,
            'preparedBy' => $preparedBy,
            'generatedAt' => new \DateTimeImmutable(),
        ]);
    }
}
