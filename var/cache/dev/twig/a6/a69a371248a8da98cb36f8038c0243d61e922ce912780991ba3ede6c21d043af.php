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

/* report/accounts.html.twig */
class __TwigTemplate_ca7ac387ba01fdc77b699623e706dc9eb036c57a0e0943c8ad144bf6784236c7 extends \Twig\Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "report/accounts.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "report/accounts.html.twig"));

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
    <div class=\"page-container\" style=\"height: 100vh;\">
        <div class=\"page-sidebar page-sidebar-fixed scroll\">
            <ul class=\"x-navigation\">
                <li class=\"xn-logo\">
                    <a href=\"/\">Top Tier</a>
                    <a href=\"#\" class=\"x-navigation-control\"></a>
                </li>
                <li class=\"xn-title\">
                    Navigation
                </li>
                <li>
                    <a href=\"/dashboard\">
                        <span class=\"fa fa-desktop\"></span>
                        <span class=\"xn-text\">Dashboard</span>
                    </a>
                </li>
                <li class=\"xn-openable active\">
                    <a href=\"#\">
                        <span class=\"fa fa-cogs\"></span>
                        <span class=\"xn-text\">Accounts</span>
                    </a>
                    <ul>
                        <li>
                            <a href=\"/accounts-add\">
                                <span class=\"fa fa-bell-o\"></span>
                                <span class=\"xn-text\">Add Account</span>
                            </a>
                        </li>
                        <li>
                            <a href=\"/accounts\">
                                <span class=\"fa fa-users\"></span>
                                <span class=\"xn-text\">Accounts</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class=\"xn-title\">
                    Profile
                </li>
                <li>
                    <a href=\"/logout\">
                        <span class=\"glyphicon glyphicon-log-out\"></span>
                        <span class=\"xn-text\">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class=\"page-content\">
            <div class=\"page-title\">
                <h2>Accounts</h2>
            </div>
            <div class=\"row\">
                <div class=\"col-md-12\">
                    <div class=\"panel panel-default\">
                        <div class=\"panel-body\">
                            <table class=\"table table-striped\">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ";
        // line 80
        if ((twig_length_filter($this->env, (isset($context["accounts"]) || array_key_exists("accounts", $context) ? $context["accounts"] : (function () { throw new RuntimeError('Variable "accounts" does not exist.', 80, $this->source); })())) > 0)) {
            // line 81
            echo "                                        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["accounts"]) || array_key_exists("accounts", $context) ? $context["accounts"] : (function () { throw new RuntimeError('Variable "accounts" does not exist.', 81, $this->source); })()));
            foreach ($context['_seq'] as $context["key"] => $context["account"]) {
                // line 82
                echo "                                            <tr>
                                                <td>";
                // line 83
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "aid", [], "any", false, false, false, 83), "html", null, true);
                echo "</td>
                                                <td>";
                // line 84
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "first_name", [], "any", false, false, false, 84), "html", null, true);
                echo "</td>
                                                <td>";
                // line 85
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "last_name", [], "any", false, false, false, 85), "html", null, true);
                echo "</td>
                                                <td>";
                // line 86
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "email", [], "any", false, false, false, 86), "html", null, true);
                echo "</td>
                                                <td>";
                // line 87
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "phone", [], "any", false, false, false, 87), "html", null, true);
                echo "</td>
                                                <td>
                                                    <a href=\"/accounts-view?aid=";
                // line 89
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "aid", [], "any", false, false, false, 89), "html", null, true);
                echo "\">
                                                        <span class=\"fa fa-pencil\"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['account'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 95
            echo "                                    ";
        } else {
            // line 96
            echo "                                        <tr>
                                            <td colspan=\"6\">No Accounts Found</td>
                                        </tr>
                                    ";
        }
        // line 100
        echo "                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    ";
        // line 109
        $this->loadTemplate("footer-js.html.twig", "report/accounts.html.twig", 109)->display($context);
        // line 110
        echo "
</body>
</html>";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "report/accounts.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  187 => 110,  185 => 109,  174 => 100,  168 => 96,  165 => 95,  153 => 89,  148 => 87,  144 => 86,  140 => 85,  136 => 84,  132 => 83,  129 => 82,  124 => 81,  122 => 80,  41 => 1,);
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
    <div class=\"page-container\" style=\"height: 100vh;\">
        <div class=\"page-sidebar page-sidebar-fixed scroll\">
            <ul class=\"x-navigation\">
                <li class=\"xn-logo\">
                    <a href=\"/\">Top Tier</a>
                    <a href=\"#\" class=\"x-navigation-control\"></a>
                </li>
                <li class=\"xn-title\">
                    Navigation
                </li>
                <li>
                    <a href=\"/dashboard\">
                        <span class=\"fa fa-desktop\"></span>
                        <span class=\"xn-text\">Dashboard</span>
                    </a>
                </li>
                <li class=\"xn-openable active\">
                    <a href=\"#\">
                        <span class=\"fa fa-cogs\"></span>
                        <span class=\"xn-text\">Accounts</span>
                    </a>
                    <ul>
                        <li>
                            <a href=\"/accounts-add\">
                                <span class=\"fa fa-bell-o\"></span>
                                <span class=\"xn-text\">Add Account</span>
                            </a>
                        </li>
                        <li>
                            <a href=\"/accounts\">
                                <span class=\"fa fa-users\"></span>
                                <span class=\"xn-text\">Accounts</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class=\"xn-title\">
                    Profile
                </li>
                <li>
                    <a href=\"/logout\">
                        <span class=\"glyphicon glyphicon-log-out\"></span>
                        <span class=\"xn-text\">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class=\"page-content\">
            <div class=\"page-title\">
                <h2>Accounts</h2>
            </div>
            <div class=\"row\">
                <div class=\"col-md-12\">
                    <div class=\"panel panel-default\">
                        <div class=\"panel-body\">
                            <table class=\"table table-striped\">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {% if accounts|length > 0 %}
                                        {% for key, account in accounts %}
                                            <tr>
                                                <td>{{ account.aid }}</td>
                                                <td>{{ account.first_name }}</td>
                                                <td>{{ account.last_name }}</td>
                                                <td>{{ account.email }}</td>
                                                <td>{{ account.phone }}</td>
                                                <td>
                                                    <a href=\"/accounts-view?aid={{ account.aid }}\">
                                                        <span class=\"fa fa-pencil\"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                        {% endfor %}
                                    {% else %}
                                        <tr>
                                            <td colspan=\"6\">No Accounts Found</td>
                                        </tr>
                                    {% endif %}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {% include 'footer-js.html.twig' %}

</body>
</html>", "report/accounts.html.twig", "/home/web.user/app/manage.toptierfinancial.com/frantz-chery/templates/report/accounts.html.twig");
    }
}
