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

/* footer-js.html.twig */
class __TwigTemplate_2ada395703de056912e69d76e9f00ac74662d9db54afdb39d5c764d2e2762c07 extends \Twig\Template
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
        echo "    <!-- START PLUGINS -->
    <script type=\"text/javascript\" src=\"/templates/joli/js/plugins/jquery/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"/templates/joli/js/plugins/jquery/jquery-ui.min.js\"></script>
    <script type=\"text/javascript\" src=\"/templates/joli/js/plugins/bootstrap/bootstrap.min.js\"></script>        
    <!-- END PLUGINS -->

    <!-- START THIS PAGE PLUGINS-->        
    <script type='text/javascript' src='/templates/joli/js/plugins/icheck/icheck.min.js'></script>
    <script type=\"text/javascript\" src=\"/templates/joli/js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js\"></script>
    <script type=\"text/javascript\" src=\"/templates/joli/js/plugins/fileinput/fileinput.min.js\"></script>
    <script type=\"text/javascript\" src=\"/templates/joli/js/plugins/blueimp/jquery.blueimp-gallery.min.js\"></script>        
    <!-- END THIS PAGE PLUGINS-->        

    <!-- START TEMPLATE -->

    
    <script type=\"text/javascript\" src=\"/templates/joli/js/plugins.js\"></script>        
    <script type=\"text/javascript\" src=\"/templates/joli/js/actions.js\"></script>  
    <!-- END TEMPLATE -->";
    }

    public function getTemplateName()
    {
        return "footer-js.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "footer-js.html.twig", "/home/web.user/app/manage.toptierfinancial.com/frantz-chery/templates/footer-js.html.twig");
    }
}
