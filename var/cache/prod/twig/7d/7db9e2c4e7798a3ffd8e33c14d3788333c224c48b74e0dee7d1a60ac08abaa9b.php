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

/* @Twig/Exception/error.js.twig */
class __TwigTemplate_f4e94fa0dc0581b025043b4f623c4c8de5cd1c1389314be8a33a17358ab49adb extends \Twig\Template
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
        echo "/*
";
        // line 2
        echo twig_escape_filter($this->env, ($context["status_code"] ?? null), "js", null, true);
        echo " ";
        echo twig_escape_filter($this->env, ($context["status_text"] ?? null), "js", null, true);
        echo "

*/
";
    }

    public function getTemplateName()
    {
        return "@Twig/Exception/error.js.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "@Twig/Exception/error.js.twig", "/home/web.user/app/manage.toptierfinancial.com/frantz-chery/vendor/symfony/twig-bundle/Resources/views/Exception/error.js.twig");
    }
}
