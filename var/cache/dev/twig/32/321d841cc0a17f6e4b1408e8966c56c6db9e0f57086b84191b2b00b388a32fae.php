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
class __TwigTemplate_693af54d951b7853b02b048e6296c10c483526c53fa5c331608f2f012ecf74be extends \Twig\Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "security/login.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "security/login.html.twig"));

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
        if ((isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 24, $this->source); })())) {
            // line 25
            echo "        <span>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 25, $this->source); })()), "messageKey", [], "any", false, false, false, 25), "html", null, true);
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
        echo twig_escape_filter($this->env, (isset($context["last_username"]) || array_key_exists("last_username", $context) ? $context["last_username"] : (function () { throw new RuntimeError('Variable "last_username" does not exist.', 31, $this->source); })()), "html", null, true);
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
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

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
        return array (  83 => 31,  77 => 28,  74 => 27,  68 => 25,  66 => 24,  41 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
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


    {% if error %}
        <span>{{ error.messageKey }}</span>
    {% endif %}

                <form action=\"{{ path('app_login') }}\" method=\"post\" class=\"form-horizontal\">
                    <div class=\"form-group\">
                        <div class=\"col-md-12\">
                            <input type=\"text\" class=\"form-control\" name=\"_username\" value=\"{{ last_username }}\" placeholder=\"Username\"/>
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
", "security/login.html.twig", "/home/web.user/app/manage.toptierfinancial.com/frantz-chery/templates/security/login.html.twig");
    }
}
