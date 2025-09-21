<?php

use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherTrait;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class srcApp_KernelProdContainerUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    use PhpMatcherTrait;

    public function __construct(RequestContext $context)
    {
        $this->context = $context;
        $this->staticRoutes = [
            '/' => [[['_route' => 'app_root', '_controller' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\RedirectController::urlRedirectAction', 'path' => '/accounts', 'permanent' => true], null, null, null, false, false, null]],
            '/upload' => [[['_route' => 'app_upload_redirect', '_controller' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\RedirectController::urlRedirectAction', 'path' => '/accounts', 'permanent' => true], null, null, null, false, false, null]],
            '/html-report' => [[['_route' => 'app_parse_html', '_controller' => 'App\\Controller\\ParserController::__init'], null, null, null, false, false, null]],
            '/parse-html-raw' => [[['_route' => 'app_parse_html_dev', '_controller' => 'App\\Controller\\ParserController::__init_raw'], null, null, null, false, false, null]],
            '/accounts' => [[['_route' => 'app_account_list', '_controller' => 'App\\Controller\\ReportController::Accounts'], null, null, null, false, false, null]],
            '/accounts-add' => [[['_route' => 'app_account_add', '_controller' => 'App\\Controller\\ReportController::add_Accounts'], null, null, null, false, false, null]],
            '/accounts-view' => [[['_route' => 'app_account_view', '_controller' => 'App\\Controller\\ReportController::view_Accounts'], null, null, null, false, false, null]],
            '/accounts-edit' => [[['_route' => 'app_account_edit', '_controller' => 'App\\Controller\\ReportController::edit_Accounts'], null, null, null, false, false, null]],
            '/login' => [[['_route' => 'app_login', '_controller' => 'App\\Controller\\SecurityController::login'], null, null, null, false, false, null]],
            '/logout' => [[['_route' => 'app_logout', '_controller' => 'App\\Controller\\SecurityController::logout'], null, null, null, false, false, null]],
            '/dashboard' => [[['_route' => 'app_dashboard', '_controller' => 'App\\Controller\\ReportController::dashboard'], null, null, null, false, false, null]],
        ];
    }
}
