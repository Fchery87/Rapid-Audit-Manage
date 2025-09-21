<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class srcApp_KernelProdContainerUrlGenerator extends Symfony\Component\Routing\Generator\UrlGenerator
{
    private static $declaredRoutes;
    private $defaultLocale;

    public function __construct(RequestContext $context, LoggerInterface $logger = null, string $defaultLocale = null)
    {
        $this->context = $context;
        $this->logger = $logger;
        $this->defaultLocale = $defaultLocale;
        if (null === self::$declaredRoutes) {
            self::$declaredRoutes = [
        'app_root' => [[], ['_controller' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\RedirectController::urlRedirectAction', 'path' => '/accounts', 'permanent' => true], [], [['text', '/']], [], []],
        'app_upload_redirect' => [[], ['_controller' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\RedirectController::urlRedirectAction', 'path' => '/accounts', 'permanent' => true], [], [['text', '/upload']], [], []],
        'app_parse_html' => [[], ['_controller' => 'App\\Controller\\ParserController::__init'], [], [['text', '/html-report']], [], []],
        'app_parse_html_dev' => [[], ['_controller' => 'App\\Controller\\ParserController::__init_raw'], [], [['text', '/parse-html-raw']], [], []],
        'app_account_list' => [[], ['_controller' => 'App\\Controller\\ReportController::Accounts'], [], [['text', '/accounts']], [], []],
        'app_account_add' => [[], ['_controller' => 'App\\Controller\\ReportController::add_Accounts'], [], [['text', '/accounts-add']], [], []],
        'app_account_view' => [[], ['_controller' => 'App\\Controller\\ReportController::view_Accounts'], [], [['text', '/accounts-view']], [], []],
        'app_account_edit' => [[], ['_controller' => 'App\\Controller\\ReportController::edit_Accounts'], [], [['text', '/accounts-edit']], [], []],
        'app_login' => [[], ['_controller' => 'App\\Controller\\SecurityController::login'], [], [['text', '/login']], [], []],
        'app_logout' => [[], ['_controller' => 'App\\Controller\\SecurityController::logout'], [], [['text', '/logout']], [], []],
        'app_dashboard' => [[], ['_controller' => 'App\\Controller\\ReportController::dashboard'], [], [['text', '/dashboard']], [], []],
    ];
        }
    }

    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        $locale = $parameters['_locale']
            ?? $this->context->getParameter('_locale')
            ?: $this->defaultLocale;

        if (null !== $locale && null !== $name) {
            do {
                if ((self::$declaredRoutes[$name.'.'.$locale][1]['_canonical_route'] ?? null) === $name) {
                    unset($parameters['_locale']);
                    $name .= '.'.$locale;
                    break;
                }
            } while (false !== $locale = strstr($locale, '_', true));
        }

        if (!isset(self::$declaredRoutes[$name])) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        list($variables, $defaults, $requirements, $tokens, $hostTokens, $requiredSchemes) = self::$declaredRoutes[$name];

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);
    }
}
