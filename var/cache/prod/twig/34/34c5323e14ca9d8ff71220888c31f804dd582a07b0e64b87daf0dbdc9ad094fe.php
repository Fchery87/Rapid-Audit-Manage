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
class __TwigTemplate_8ed27ab72fecb59ce5922ff4cd2ac17036a73bd2c382d66306d3c1279d71c531 extends \Twig\Template
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
        if ((twig_length_filter($this->env, ($context["accounts"] ?? null)) > 0)) {
            // line 81
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["accounts"] ?? null));
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
        return array (  178 => 110,  176 => 109,  165 => 100,  159 => 96,  146 => 89,  141 => 87,  137 => 86,  133 => 85,  129 => 84,  125 => 83,  122 => 82,  118 => 81,  116 => 80,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "report/accounts.html.twig", "/home/u819198500/domains/toptierfinancialsolutions.com/public_html/manage_program/templates/report/accounts.html.twig");
    }
}
