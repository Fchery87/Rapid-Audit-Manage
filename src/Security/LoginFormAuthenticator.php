<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Security\Mfa\TotpService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepository $userRepository,
        private readonly TotpService $totpService,
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $username = trim((string) $request->request->get('_username', ''));
        $password = (string) $request->request->get('_password', '');
        $mfaCode = (string) $request->request->get('mfa_code', '');

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        $userBadge = new UserBadge($username, function (string $userIdentifier) use ($mfaCode) {
            $user = $this->userRepository->findOneBy(['username' => $userIdentifier]);
            if ($user === null) {
                throw new CustomUserMessageAuthenticationException('Invalid credentials.');
            }

            if ($user->isMfaEnabled()) {
                if (trim($mfaCode) === '') {
                    throw new CustomUserMessageAuthenticationException('Multi-factor code is required.');
                }

                if (!$this->totpService->verifyCode((string) $user->getMfaSecret(), $mfaCode)) {
                    throw new CustomUserMessageAuthenticationException('Invalid multi-factor code.');
                }
            }

            return $user;
        });

        return new Passport(
            $userBadge,
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', (string) $request->request->get('_csrf_token', '')),
                new PasswordUpgradeBadge($password, $this->userRepository),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_account_list'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }
}
