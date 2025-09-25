<?php

namespace App\Controller;

use App\Security\SensitiveDataProtector;
use App\Service\FileStorage;
use App\Service\Security\AuditTrailService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;

#[IsGranted('ROLE_ANALYST')]
class ReportController extends AbstractController
{
    public function __construct(
        private readonly FileStorage $storage,
        private readonly EntityManagerInterface $entityManager,
        private readonly SensitiveDataProtector $protector,
        private readonly AuditTrailService $auditTrail,
    ) {
    }

    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('dashboard/dashboard-sample.html.twig');
    }

    #[Route('/upload', name: 'app_upload', methods: ['GET', 'POST'])]
    public function upload(Request $request): Response
    {
        $filename = null;
        $errors = [];
        $message = '';

        if ($request->isMethod('POST') && $request->request->get('upload') === 'true') {
            if (!$this->isCsrfTokenValid('global_upload', (string) $request->request->get('_token'))) {
                $errors[] = 'Invalid CSRF token.';
            }

            $uploadedFile = $request->files->get('html_file');
            if (!$uploadedFile) {
                $errors[] = 'No file sent';
            }

            if ($errors === [] && $uploadedFile) {
                try {
                    $filename = $this->storage->storeUploadedHtml($uploadedFile);
                    $message = 'Upload successful!';
                } catch (\RuntimeException $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }

        return $this->render('report/report.html.twig', [
            'filename' => $filename,
            'errors' => $errors,
            'message' => $message,
            'files' => $this->storage->listHtmlFilenames(),
        ]);
    }

    #[Route('/accounts', name: 'app_account_list', methods: ['GET'])]
    public function listAccounts(): Response
    {
        return $this->render('report/accounts.html.twig', [
            'accounts' => $this->fetchAccounts(),
        ]);
    }

    #[Route('/accounts-add', name: 'app_account_add', methods: ['GET', 'POST'])]
    public function addAccount(Request $request): Response
    {
        $messages = [];
        $errors = [];

        $parameters = $this->collectAccountParameters($request);

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('account_add_form', (string) $request->request->get('_token'))) {
                $errors[] = 'Invalid CSRF token.';
            }

            $errors = array_merge($errors, $this->validateAccountData($parameters));

            if (!$errors && $this->emailExists($parameters['email'])) {
                $errors[] = 'Email address already exists';
            }

            if (!$errors) {
                $payload = $this->protector->encryptAccountPayload([
                    'first_name' => $parameters['first_name'],
                    'last_name' => $parameters['last_name'],
                    'email' => $parameters['email'],
                    'phone' => $parameters['phone'],
                    'address1' => $parameters['address1'],
                    'address2' => $parameters['address2'],
                    'city' => $parameters['city'],
                    'state' => $parameters['state'],
                    'zip' => $parameters['zip'],
                    'social' => $parameters['social'],
                    'credit_company' => $parameters['credit_company'],
                    'credit_company_user' => $parameters['credit_company_user'],
                    'credit_company_password' => $parameters['credit_company_password'],
                    'credit_company_code' => $parameters['credit_company_code'],
                ]);
                $payload['created'] = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

                $this->connection()->insert('accounts', $payload);

                $emailHash = $this->hashIdentifier($parameters['email']);
                $insertId = $this->connection()->lastInsertId();
                $accountAid = is_numeric($insertId) ? (int) $insertId : null;

                $this->auditTrail->record(
                    eventType: 'analyst.account.created',
                    accountAid: $accountAid,
                    actorId: $this->resolveActorId(),
                    metadata: array_filter([
                        'email_hash' => $emailHash,
                        'route' => 'app_account_add',
                    ], static fn($value) => $value !== null),
                    subjectType: 'account',
                    subjectId: $accountAid !== null ? (string) $accountAid : ($emailHash ?? 'unknown'),
                    actorType: 'analyst'
                );

                $messages[] = 'Account successfully added!';
            }
        }

        return $this->render('report/accounts-add.html.twig', [
            'errors' => $errors,
            'messages' => $messages,
            'parameters' => $parameters,
        ]);
    }

    #[Route('/accounts-view', name: 'app_account_view', methods: ['GET', 'POST'])]
    public function viewAccount(Request $request): Response
    {
        $aid = $request->query->getInt('aid');
        if ($aid <= 0) {
            return $this->render('report/accounts-view-not-found.html.twig');
        }

        $account = $this->fetchAccount($aid);
        if (!$account) {
            return $this->render('report/accounts-view-not-found.html.twig');
        }

        $this->auditTrail->record(
            eventType: 'analyst.account.viewed',
            accountAid: $aid,
            actorId: $this->resolveActorId(),
            metadata: ['route' => 'app_account_view'],
            subjectType: 'account',
            subjectId: (string) $aid,
            actorType: 'analyst'
        );

        $errors = [];
        $messages = [];

        if ($request->isMethod('POST') && $request->request->get('upload')) {
            if (!$this->isCsrfTokenValid('account_upload', (string) $request->request->get('_token'))) {
                $errors[] = 'Invalid CSRF token.';
            }

            $uploadedFile = $request->files->get('html_file');
            if (!$uploadedFile) {
                $errors[] = 'No file sent';
            }

            if ($errors === [] && $uploadedFile) {
                try {
                    $storedName = $this->storage->storeUploadedHtml($uploadedFile);

                    if ($this->reportExistsForAccount($aid, $storedName)) {
                        $errors[] = 'File already exists for this user.';
                    } else {
                        $this->connection()->insert('account_files', [
                            'aid' => $aid,
                            'filename' => $storedName,
                            'added' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                        ]);
                        $this->auditTrail->record(
                            eventType: 'analyst.account.document_uploaded',
                            accountAid: $aid,
                            actorId: $this->resolveActorId(),
                            metadata: [
                                'filename' => $storedName,
                                'route' => 'app_account_view',
                            ],
                            subjectType: 'account_file',
                            subjectId: $storedName,
                            actorType: 'analyst'
                        );
                        $messages[] = 'Upload successful!';
                    }
                } catch (\RuntimeException $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }

        return $this->render('report/accounts-view.html.twig', [
            'account_info' => $account,
            'errors' => $errors,
            'messages' => $messages,
            'files' => $this->fetchAccountFiles($aid),
        ]);
    }

    #[Route('/accounts-edit', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function editAccount(Request $request): Response
    {
        $aid = $request->query->getInt('aid');
        if ($aid <= 0) {
            return $this->render('report/accounts-view-not-found.html.twig');
        }

        $account = $this->fetchAccount($aid);
        if (!$account) {
            return $this->render('report/accounts-view-not-found.html.twig');
        }

        $messages = [];
        $errors = [];

        $parameters = $this->collectAccountParameters($request, $account);

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('account_edit_form', (string) $request->request->get('_token'))) {
                $errors[] = 'Invalid CSRF token.';
            }

            $errors = array_merge($errors, $this->validateAccountData($parameters));

            if (!$errors && $this->emailExists($parameters['email'], $aid)) {
                $errors[] = 'Email address already exists';
            }

            if (!$errors) {
                $payload = $this->protector->encryptAccountPayload([
                    'first_name' => $parameters['first_name'],
                    'last_name' => $parameters['last_name'],
                    'email' => $parameters['email'],
                    'phone' => $parameters['phone'],
                    'address1' => $parameters['address1'],
                    'address2' => $parameters['address2'],
                    'city' => $parameters['city'],
                    'state' => $parameters['state'],
                    'zip' => $parameters['zip'],
                    'social' => $parameters['social'],
                    'credit_company' => $parameters['credit_company'],
                    'credit_company_user' => $parameters['credit_company_user'],
                    'credit_company_password' => $parameters['credit_company_password'],
                    'credit_company_code' => $parameters['credit_company_code'],
                ]);

                $this->connection()->update('accounts', $payload, ['aid' => $aid]);

                $emailHash = $this->hashIdentifier($parameters['email']);
                $this->auditTrail->record(
                    eventType: 'analyst.account.updated',
                    accountAid: $aid,
                    actorId: $this->resolveActorId(),
                    metadata: array_filter([
                        'email_hash' => $emailHash,
                        'route' => 'app_account_edit',
                    ], static fn($value) => $value !== null),
                    subjectType: 'account',
                    subjectId: (string) $aid,
                    actorType: 'analyst'
                );

                $messages[] = 'Account successfully updated!';
            }
        }

        return $this->render('report/accounts-edit.html.twig', [
            'errors' => $errors,
            'messages' => $messages,
            'parameters' => $parameters,
        ]);
    }

    private function resolveActorId(): string
    {
        $user = $this->getUser();

        if ($user instanceof UserInterface) {
            return $user->getUserIdentifier();
        }

        return 'anonymous';
    }

    private function hashIdentifier(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return hash('sha256', mb_strtolower($value));
    }

    private function connection(): Connection
    {
        return $this->entityManager->getConnection();
    }

    private function fetchAccounts(): array
    {
        $sql = 'SELECT * FROM accounts';

        $rows = $this->connection()->executeQuery($sql)->fetchAllAssociative();
        $accounts = array_map(fn (array $row): array => $this->protector->decryptAccountRecord($row), $rows);

        usort($accounts, static function (array $a, array $b): int {
            return strcasecmp((string) ($b['last_name'] ?? ''), (string) ($a['last_name'] ?? ''));
        });

        return $accounts;
    }

    private function fetchAccount(int $aid): ?array
    {
        $sql = 'SELECT * FROM accounts WHERE aid = :aid LIMIT 1';

        $result = $this->connection()->executeQuery($sql, ['aid' => $aid]);

        $account = $result->fetchAssociative();

        return $account ? $this->protector->decryptAccountRecord($account) : null;
    }

    private function fetchAccountFiles(int $aid): array
    {
        $sql = 'SELECT * FROM account_files WHERE aid = :aid ORDER BY added DESC';

        return $this->connection()->executeQuery($sql, ['aid' => $aid])->fetchAllAssociative();
    }

    /**
     * @param array{first_name:string,last_name:string,email:string,phone:string,address1:string,address2:string,city:string,state:string,zip:string,social:string,credit_company:string,credit_company_user:string,credit_company_password:string,credit_company_code:string} $defaults
     */
    private function collectAccountParameters(Request $request, array $defaults = []): array
    {
        $fields = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'address1',
            'address2',
            'city',
            'state',
            'zip',
            'social',
            'credit_company',
            'credit_company_user',
            'credit_company_password',
            'credit_company_code',
        ];

        $parameters = [];
        foreach ($fields as $field) {
            $parameters[$field] = trim((string) ($request->request->get($field) ?? ($defaults[$field] ?? '')));
        }

        return $parameters;
    }

    private function validateAccountData(array $parameters): array
    {
        $errors = [];

        if (!$this->isValidName($parameters['first_name'])) {
            $errors[] = 'Invalid First Name.';
        }
        if (!$this->isValidName($parameters['last_name'])) {
            $errors[] = 'Invalid Last Name';
        }
        if (!$this->isValidEmail($parameters['email'])) {
            $errors[] = 'Invalid Email';
        }
        if (!$this->isValidPhone($parameters['phone'])) {
            $errors[] = 'Invalid Phone Number';
        }
        if (!$this->hasMinLength($parameters['address1'], 5)) {
            $errors[] = 'Invalid Address1';
        }
        if (!$this->hasMinLength($parameters['city'], 3)) {
            $errors[] = 'Invalid City';
        }
        if (!$this->isValidZip($parameters['zip'])) {
            $errors[] = 'Invalid Zip Code';
        }
        if (!$this->isValidSocial($parameters['social'])) {
            $errors[] = 'Invalid Social';
        }

        return $errors;
    }

    private function emailExists(string $email, ?int $excludeAid = null): bool
    {
        $lookup = $this->protector->buildLookupValues($email);

        $conditions = ['email = :plainEmail'];
        $params = [
            'plainEmail' => $email,
        ];

        if ($lookup['pattern'] !== '') {
            $conditions[] = 'email LIKE :encryptedEmailPattern';
            $params['encryptedEmailPattern'] = $lookup['pattern'];
        }

        if ($lookup['legacy'] !== '') {
            $conditions[] = 'email = :legacyEncryptedEmail';
            $params['legacyEncryptedEmail'] = $lookup['legacy'];
        }

        $sql = sprintf(
            'SELECT COUNT(*) FROM accounts WHERE (%s)',
            implode(' OR ', $conditions)
        );

        if ($excludeAid !== null) {
            $sql .= ' AND aid <> :aid';
            $params['aid'] = $excludeAid;
        }

        return (int) $this->connection()->fetchOne($sql, $params) > 0;
    }

    private function reportExistsForAccount(int $aid, string $filename): bool
    {
        $sql = 'SELECT COUNT(*) FROM account_files WHERE aid = :aid AND filename = :filename';

        return (int) $this->connection()->fetchOne($sql, [
            'aid' => $aid,
            'filename' => $filename,
        ]) > 0;
    }

    private function isValidName(?string $value): bool
    {
        return $this->hasMinLength($value, 3);
    }

    private function hasMinLength(?string $value, int $length): bool
    {
        return mb_strlen(trim((string) $value)) >= $length;
    }

    private function isValidEmail(?string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function isValidPhone(?string $value): bool
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        return in_array(strlen($digits), [7, 10], true);
    }

    private function isValidZip(?string $value): bool
    {
        return preg_match('/^\d{5}$/', (string) $value) === 1;
    }

    private function isValidSocial(?string $value): bool
    {
        return mb_strlen(trim((string) $value)) === 4;
    }

}

