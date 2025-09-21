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

/* report/report.html.twig */
class __TwigTemplate_91a15b5606d27307932342d50c8068240af179b7824f1f6d83b7179b8f93bbc1 extends \Twig\Template
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
<html lang=\"en\">
  <head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">

    <title>Report Upload</title>

    <link rel=\"stylesheet\" href=\"/templates/joli/css/theme-blue.css\">

  </head>
  <body>
  <div class=\"page-container\">
    <div class=\"page-sidebar page-sidebar-fixed scroll\">
        <ul class=\"x-navigation\">
            <li class=\"xn-logo\">
                <a href=\"/\">Top Tier</a>
            </li>
            <li class=\"xn-title\">Navigation</li>
            
        </ul>
    </div>

    <div class=\"container\">
        <img class=\"center-block\" src=\"images/logo-new.png\" width=\"500\" />
    </div>

    ";
        // line 28
        if ((twig_length_filter($this->env, ($context["errors"] ?? null)) > 0)) {
            // line 29
            echo "        <div id=\"errors\">
            <ul>
            ";
            // line 31
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["errors"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["error"]) {
                // line 32
                echo "                <li>";
                echo twig_escape_filter($this->env, $context["error"], "html", null, true);
                echo "</li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['error'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 34
            echo "            </ul>
        </div>
    ";
        }
        // line 37
        echo "    <div class=\"container\">
        <form id=\"uploader\" method=\"post\" action=\"/upload\" enctype=\"multipart/form-data\">
            <fieldset>
                <legend>Add HTML File</legend>
                <div class=\"form-group has-success\">
                    <h5>";
        // line 42
        echo twig_escape_filter($this->env, ($context["message"] ?? null), "html", null, true);
        echo "</h5>
                </div>
                <div class=\"form-group\"> 
                    <input type=\"hidden\" name=\"upload\" value=\"true\" />
                    <label>File</label><input type=\"file\" name=\"html_file\" accept=\"text/html\" />
                    <span class=\"help-block\">Select an HTML file to upload</span>
                    <input type=\"submit\" name=\"submit\" value=\"Upload\" />
                </div>
            </fieldset>
        </form>
    </div>

    <div class=\"container\">
        <h3>Files</h3>
        ";
        // line 56
        if ((twig_length_filter($this->env, ($context["files"] ?? null)) > 0)) {
            // line 57
            echo "            <ol>
            ";
            // line 58
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["files"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["file"]) {
                // line 59
                echo "                <li><a href=\"/html-report?file=";
                echo twig_escape_filter($this->env, $context["file"], "html", null, true);
                echo "\" target=\"_blank\">";
                echo twig_escape_filter($this->env, $context["file"], "html", null, true);
                echo "</a></li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 61
            echo "            </ol>
        ";
        }
        // line 63
        echo "    </div>
    </div>
  </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "report/report.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  136 => 63,  132 => 61,  121 => 59,  117 => 58,  114 => 57,  112 => 56,  95 => 42,  88 => 37,  83 => 34,  74 => 32,  70 => 31,  66 => 29,  64 => 28,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "report/report.html.twig", "/home/web.user/app/manage.toptierfinancial.com/frantz-chery/templates/report/report.html.twig");
    }
}
