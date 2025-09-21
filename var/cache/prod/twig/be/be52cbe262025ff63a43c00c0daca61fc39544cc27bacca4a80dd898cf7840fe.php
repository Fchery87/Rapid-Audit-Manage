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
class __TwigTemplate_9464375c7b2765f739654ce6e150549aa7fc6641fa4442d1d2b6ae0d2c250ee5 extends \Twig\Template
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
        return new Source("", "footer-js.html.twig", "/home/u819198500/domains/toptierfinancialsolutions.com/public_html/manage_program/templates/footer-js.html.twig");
    }
}
