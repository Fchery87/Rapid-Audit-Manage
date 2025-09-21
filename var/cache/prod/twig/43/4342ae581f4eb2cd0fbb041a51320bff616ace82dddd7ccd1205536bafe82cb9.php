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

/* report/accounts-view.html.twig */
class __TwigTemplate_fe133764ff29d9f0a68a7cedd16faa185014a9acc409adb8b4f8c2acd56b8f3d extends \Twig\Template
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
                <h2>Account: ";
        // line 62
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "first_name", [], "any", false, false, false, 62), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "last_name", [], "any", false, false, false, 62), "html", null, true);
        echo "</h2>
            </div>
            
                
            ";
        // line 66
        if ((twig_length_filter($this->env, ($context["errors"] ?? null)) > 0)) {
            // line 67
            echo "                <div class=\"row\">
                    <div class=\"col-md-12\">
                        <div class=\"alert alert-danger\">
                            <ul>
                            ";
            // line 71
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["errors"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["error"]) {
                // line 72
                echo "                                <li>";
                echo twig_escape_filter($this->env, $context["error"], "html", null, true);
                echo "</li>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['error'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 74
            echo "                            </ul>
                        </div>
                    </div>
                </div>
            ";
        }
        // line 79
        echo "            ";
        if ((twig_length_filter($this->env, ($context["messages"] ?? null)) > 0)) {
            // line 80
            echo "                <div class=\"row\">
                    <div class=\"col-md-12\">
                        <div class=\"alert alert-success\">
                            <ul>
                            ";
            // line 84
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["messages"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["message"]) {
                // line 85
                echo "                                <li>";
                echo twig_escape_filter($this->env, $context["message"], "html", null, true);
                echo "</li>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['message'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 87
            echo "                            </ul>
                        </div>
                    </div>
                </div>
            ";
        }
        // line 92
        echo "            <div class=\"row\">
                <div class=\"col-md-8\">
                    <div class=\"panel panel-default\">
                        <div class=\"panel-body\">
                            <h3>Add Report</h3>
                            <form id=\"uploader\" method=\"post\" action=\"/accounts-view?aid=";
        // line 97
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "aid", [], "any", false, false, false, 97), "html", null, true);
        echo "\" enctype=\"multipart/form-data\" class=\"horizontal\">
                                <input type=\"hidden\" name=\"upload\" value=\"true\" />
                                <div class=\"form-group\"> 
                                    <label for=\"html_file\" class=\"col-md-3 col-xs-12 control-label\">File</label>
                                    <div class=\"col-md-12\">
                                        <input id=\"file-uploader\" type=\"file\" class=\"file\" data-preview-file-type=\"html\" accept=\"text/html, text/htm\" name=\"html_file\" >
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class=\"col-md-4\">
                    <div class=\"panel panel-default\">
                        <div class=\"panel-body\">
                            <h3>Customer Information</h3>
                            <table>
                                <tbody>
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>";
        // line 117
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "first_name", [], "any", false, false, false, 117), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "last_name", [], "any", false, false, false, 117), "html", null, true);
        echo "</td>
                                    </tr>
                                    <tr>
                                        <td style=\"vertical-align: top;\"><strong>Address:</strong></td>
                                        <td>
                                            ";
        // line 122
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "address1", [], "any", false, false, false, 122), "html", null, true);
        echo "<br/>
                                            ";
        // line 123
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "address2", [], "any", false, false, false, 123), "html", null, true);
        echo "<br/>
                                            ";
        // line 124
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "city", [], "any", false, false, false, 124), "html", null, true);
        echo ", ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "state", [], "any", false, false, false, 124), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "zip", [], "any", false, false, false, 124), "html", null, true);
        echo "
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>";
        // line 129
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "phone", [], "any", false, false, false, 129), "html", null, true);
        echo "</td>
                                    </tr>                          
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>";
        // line 133
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "email", [], "any", false, false, false, 133), "html", null, true);
        echo "</td>
                                    </tr>                          
                                    <tr>
                                        <td><strong>Social</strong></td>
                                        <td>XXX-XX-";
        // line 137
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "social", [], "any", false, false, false, 137), "html", null, true);
        echo "</td>
                                    </tr>    
                                    <tr>
                                        <td><strong>Credit Reporting Company:</strong></td>
                                        <td>";
        // line 141
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "credit_company", [], "any", false, false, false, 141), "html", null, true);
        echo "</td>
                                    </tr>    
                                    <tr>
                                        <td><strong>Credit Reporting Username:</strong></td>
                                        <td>";
        // line 145
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "credit_company_user", [], "any", false, false, false, 145), "html", null, true);
        echo "</td>
                                    </tr>         
                                    <tr>
                                        <td><strong>Credit Reporting Password:</strong></td>
                                        <td>";
        // line 149
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "credit_company_password", [], "any", false, false, false, 149), "html", null, true);
        echo "</td>
                                    </tr>      
                                    <tr>
                                        <td><strong>Credit Reporting Security Code:</strong></td>
                                        <td>";
        // line 153
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "credit_company_code", [], "any", false, false, false, 153), "html", null, true);
        echo "</td>
                                    </tr>            
                                    <tr>
                                        <td colspan=\"2\"><a href=\"/accounts-edit?aid=";
        // line 156
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account_info"] ?? null), "aid", [], "any", false, false, false, 156), "html", null, true);
        echo "\">Edit</a></td>
                                    </tr>                                                                 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class=\"row\">
                <div class=\"col-md-12\">
                    <div class=\"panel panel-default\">
                        <div class=\"panel-body\">
                            <h3>Reports</h3>
                            <table  class=\"table table-striped\">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>File</th>
                                        <th>Added</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ";
        // line 178
        if ((twig_length_filter($this->env, ($context["files"] ?? null)) > 0)) {
            // line 179
            echo "                                        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["files"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["file"]) {
                // line 180
                echo "                                            <tr>
                                                <td>";
                // line 181
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "fid", [], "any", false, false, false, 181), "html", null, true);
                echo "</td>
                                                <td><a href=\"/html-report?file=";
                // line 182
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "filename", [], "any", false, false, false, 182), "html", null, true);
                echo "\" target=\"_blank\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "filename", [], "any", false, false, false, 182), "html", null, true);
                echo "</a></td>
                                                <td>";
                // line 183
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "added", [], "any", false, false, false, 183), "html", null, true);
                echo "</td>
                                            </tr>
                                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 186
            echo "                                    ";
        } else {
            // line 187
            echo "                                        <tr><td colspan=\"3\">No Reports Found!</td></tr>
                                    ";
        }
        // line 189
        echo "                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    ";
        // line 197
        $this->loadTemplate("footer-js.html.twig", "report/accounts-view.html.twig", 197)->display($context);
        // line 198
        echo "</body>
</html>";
    }

    public function getTemplateName()
    {
        return "report/accounts-view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  345 => 198,  343 => 197,  333 => 189,  329 => 187,  326 => 186,  317 => 183,  311 => 182,  307 => 181,  304 => 180,  299 => 179,  297 => 178,  272 => 156,  266 => 153,  259 => 149,  252 => 145,  245 => 141,  238 => 137,  231 => 133,  224 => 129,  212 => 124,  208 => 123,  204 => 122,  194 => 117,  171 => 97,  164 => 92,  157 => 87,  148 => 85,  144 => 84,  138 => 80,  135 => 79,  128 => 74,  119 => 72,  115 => 71,  109 => 67,  107 => 66,  98 => 62,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "report/accounts-view.html.twig", "/home/web.user/app/manage.toptierfinancial.com/frantz-chery/templates/report/accounts-view.html.twig");
    }
}
