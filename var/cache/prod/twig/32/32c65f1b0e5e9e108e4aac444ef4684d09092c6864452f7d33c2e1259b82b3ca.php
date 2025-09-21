<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* security/login.html.twig */
class __TwigTemplate_28f8bf93797780c0bba15d8ba7e81fb9a1db5baf6bf2dd455d102b85f390723d extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\" class=\"body-full-height\">
  <head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">

    <title>Accounts</title>

    <link rel=\"stylesheet\" href=\"/templates/joli/css/theme-blue.css\">

  </head>
  <body>
    <div class=\"login-container\">
        <div class=\"login-box animated fadeInDown\">
            <div class=\"login-logo\"></div>

            <div class=\"login-body\">

                <div class=\"login-title\">
                    Please login to continue.
                </div>


    ";
        // line 24
        if (($context["error"] ?? null)) {
            // line 25
            echo "        <span>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["error"] ?? null), "messageKey", [], "any", false, false, false, 25), "html", null, true);
            echo "</span>
    ";
        }
        // line 27
        echo "
                <form action=\"";
        // line 28
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_login");
        echo "\" method=\"post\" class=\"form-horizontal\">
                    <div class=\"form-group\">
                        <div class=\"col-md-12\">
                            <input type=\"text\" class=\"form-control\" name=\"_username\" value=\"";
        // line 31
        echo twig_escape_filter($this->env, ($context["last_username"] ?? null), "html", null, true);
        echo "\" placeholder=\"Username\"/>
                        </div>
                    </div>
                    <div class=\"form-group\">
                        <div class=\"col-md-12\">
                            <input type=\"password\" class=\"form-control\" name=\"_password\" placeholder=\"Password\" />
                        </div>
                    </div>
                    <div class=\"form-group\">
                        <div class=\"col-md-6\">
                            &nbsp;
                        </div>
                        <div class=\"col-md-6\">
                            <button type=\"submit\" class=\"btn btn-info btn-block\">Login</button>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class=\"login-footer\">
                <div class=\"pull-left\">
                    &copy; 2019 Top Tier Financial Solutions.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "security/login.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  77 => 31,  71 => 28,  68 => 27,  62 => 25,  60 => 24,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "security/login.html.twig", "/home/u819198500/domains/toptierfinancialsolutions.com/public_html/manage_program/templates/security/login.html.twig");
    }
}
