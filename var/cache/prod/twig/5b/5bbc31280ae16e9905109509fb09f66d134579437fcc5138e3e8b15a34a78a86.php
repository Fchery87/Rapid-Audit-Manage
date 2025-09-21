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

/* report/accounts-edit.html.twig */
class __TwigTemplate_e6dfde941e64f5922afd5fe14ebdd410023bb49a4426d0b86e73b80462ece46c extends \Twig\Template
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

    <title>Edit Account</title>

    <link rel=\"stylesheet\" href=\"/templates/joli/css/theme-blue.css\">

  </head>
  <body>
    <div class=\"page-container\" style=\"height: 100vh;\">
        <div class=\"page-sidebar page-sidebar-fixed scroll\">
            <ul class=\"x-navigation\">
                <li class=\"xn-logo\">
                    <a href=\"/\">Financial</a>
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
                <h2>Edit Account</h2>
            </div>
            ";
        // line 64
        if ((twig_length_filter($this->env, ($context["errors"] ?? null)) > 0)) {
            // line 65
            echo "                <div class=\"row\">
                    <div class=\"col-md-12\">
                        <div class=\"alert alert-danger\">
                            <ul>
                            ";
            // line 69
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["errors"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["error"]) {
                // line 70
                echo "                                <li>";
                echo twig_escape_filter($this->env, $context["error"], "html", null, true);
                echo "</li>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['error'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 72
            echo "                            </ul>
                        </div>
                    </div>
                </div>
            ";
        }
        // line 77
        echo "            ";
        if ((twig_length_filter($this->env, ($context["messages"] ?? null)) > 0)) {
            // line 78
            echo "                <div class=\"row\">
                    <div class=\"col-md-12\">
                        <div class=\"alert alert-success\">
                            <ul>
                            ";
            // line 82
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["messages"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["message"]) {
                // line 83
                echo "                                <li>";
                echo twig_escape_filter($this->env, $context["message"], "html", null, true);
                echo "</li>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['message'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 85
            echo "                            </ul>
                        </div>
                    </div>
                </div>
            ";
        }
        // line 90
        echo "            <div class=\"row\">
                <div class=\"col-md-12\">
                    <form method=\"post\" action=\"/accounts-edit?aid=";
        // line 92
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "aid", [], "any", false, false, false, 92), "html", null, true);
        echo "\" class=\"form-horizontal\">
                        <div class=\"panel panel-default\">
                            <div class=\"panel-body\">
                                <div class=\"form-group\">
                                    <label for=\"first_name\" class=\"col-md-3 col-xs-12 control-label\">First Name:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>
                                            <input type=\"text\" name=\"first_name\" class=\"form-control\" value=\"";
        // line 104
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "first_name", [], "any", false, false, false, 104), "html", null, true);
        echo "\" placeholder=\"First Name\" />
                                        </div>
                                    </div>
                                </div>

                                <div class=\"form-group\">
                                    <label for=\"last_name\" class=\"col-md-3 col-xs-12 control-label\">Last Name:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\" >
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>
                                            <input type=\"text\" name=\"last_name\" class=\"form-control\" value=\"";
        // line 118
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "last_name", [], "any", false, false, false, 118), "html", null, true);
        echo "\" placeholder=\"Last Name\" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class=\"form-group\">
                                    <label for=\"email\" class=\"col-md-3 col-xs-12 control-label\">Email:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-envelope\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"email\" name=\"email\" class=\"form-control\" value=\"";
        // line 132
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "email", [], "any", false, false, false, 132), "html", null, true);
        echo "\" placeholder=\"Email\" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class=\"form-group\">
                                    <label for=\"phone\" class=\"col-md-3 col-xs-12 control-label\">Phone:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-phone\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"text\" name=\"phone\" class=\"form-control\" value=\"";
        // line 146
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "phone", [], "any", false, false, false, 146), "html", null, true);
        echo "\" placeholder=\"Phone\" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class=\"form-group\">
                                    <label for=\"address1\" class=\"col-md-3 col-xs-12 control-label\">Address 1:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"text\" name=\"address1\" class=\"form-control\" value=\"";
        // line 160
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "address1", [], "any", false, false, false, 160), "html", null, true);
        echo "\" placeholder=\"Address\" />
                                        </div>
                                    </div>
                                </div>

                                
                                <div class=\"form-group\">
                                    <label for=\"address2\" class=\"col-md-3 col-xs-12 control-label\">Address 2:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"text\" name=\"address2\" class=\"form-control\" value=\"";
        // line 175
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "address2", [], "any", false, false, false, 175), "html", null, true);
        echo "\" placeholder=\"Apt # / Suite #\" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class=\"form-group\">
                                    <label for=\"city\" class=\"col-md-3 col-xs-12 control-label\">City:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"text\" name=\"city\" class=\"form-control\" value=\"";
        // line 189
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "city", [], "any", false, false, false, 189), "html", null, true);
        echo "\" placeholder=\"City\" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class=\"form-group\">
                                    <label for=\"state\" class=\"col-md-3 col-xs-12 control-label\">State:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <select name=\"state\" class=\"form-control select\">
                                            <option value=\"AL\" ";
        // line 198
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 198) == "AL")) {
            echo " selected ";
        }
        echo ">Alabama</option>
                                            <option value=\"AK\" ";
        // line 199
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 199) == "AK")) {
            echo " selected ";
        }
        echo ">Alaska</option>
                                            <option value=\"AZ\" ";
        // line 200
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 200) == "AZ")) {
            echo " selected ";
        }
        echo ">Arizona</option>
                                            <option value=\"AR\" ";
        // line 201
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 201) == "AR")) {
            echo " selected ";
        }
        echo ">Arkansas</option>
                                            <option value=\"CA\" ";
        // line 202
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 202) == "CA")) {
            echo " selected ";
        }
        echo ">California</option>
                                            <option value=\"CO\" ";
        // line 203
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 203) == "CO")) {
            echo " selected ";
        }
        echo ">Colorado</option>
                                            <option value=\"CT\" ";
        // line 204
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 204) == "CT")) {
            echo " selected ";
        }
        echo ">Connecticut</option>
                                            <option value=\"DE\" ";
        // line 205
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 205) == "DE")) {
            echo " selected ";
        }
        echo ">Delaware</option>
                                            <option value=\"DC\" ";
        // line 206
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 206) == "DC")) {
            echo " selected ";
        }
        echo ">District Of Columbia</option>
                                            <option value=\"FL\" ";
        // line 207
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 207) == "FL")) {
            echo " selected ";
        }
        echo ">Florida</option>
                                            <option value=\"GA\" ";
        // line 208
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 208) == "GA")) {
            echo " selected ";
        }
        echo ">Georgia</option>
                                            <option value=\"HI\" ";
        // line 209
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 209) == "HI")) {
            echo " selected ";
        }
        echo ">Hawaii</option>
                                            <option value=\"ID\" ";
        // line 210
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 210) == "ID")) {
            echo " selected ";
        }
        echo ">Idaho</option>
                                            <option value=\"IL\" ";
        // line 211
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 211) == "IL")) {
            echo " selected ";
        }
        echo ">Illinois</option>
                                            <option value=\"IN\" ";
        // line 212
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 212) == "IN")) {
            echo " selected ";
        }
        echo ">Indiana</option>
                                            <option value=\"IA\" ";
        // line 213
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 213) == "IA")) {
            echo " selected ";
        }
        echo ">Iowa</option>
                                            <option value=\"KS\" ";
        // line 214
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 214) == "KS")) {
            echo " selected ";
        }
        echo ">Kansas</option>
                                            <option value=\"KY\" ";
        // line 215
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 215) == "KY")) {
            echo " selected ";
        }
        echo ">Kentucky</option>
                                            <option value=\"LA\" ";
        // line 216
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 216) == "LA")) {
            echo " selected ";
        }
        echo ">Louisiana</option>
                                            <option value=\"ME\" ";
        // line 217
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 217) == "ME")) {
            echo " selected ";
        }
        echo ">Maine</option>
                                            <option value=\"MD\" ";
        // line 218
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 218) == "MD")) {
            echo " selected ";
        }
        echo ">Maryland</option>
                                            <option value=\"MA\" ";
        // line 219
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 219) == "MA")) {
            echo " selected ";
        }
        echo ">Massachusetts</option>
                                            <option value=\"MI\" ";
        // line 220
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 220) == "MI")) {
            echo " selected ";
        }
        echo ">Michigan</option>
                                            <option value=\"MN\" ";
        // line 221
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 221) == "MN")) {
            echo " selected ";
        }
        echo ">Minnesota</option>
                                            <option value=\"MS\" ";
        // line 222
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 222) == "MS")) {
            echo " selected ";
        }
        echo ">Mississippi</option>
                                            <option value=\"MO\" ";
        // line 223
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 223) == "MO")) {
            echo " selected ";
        }
        echo ">Missouri</option>
                                            <option value=\"MT\" ";
        // line 224
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 224) == "MT")) {
            echo " selected ";
        }
        echo ">Montana</option>
                                            <option value=\"NE\" ";
        // line 225
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 225) == "NE")) {
            echo " selected ";
        }
        echo ">Nebraska</option>
                                            <option value=\"NV\" ";
        // line 226
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 226) == "NV")) {
            echo " selected ";
        }
        echo ">Nevada</option>
                                            <option value=\"NH\" ";
        // line 227
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 227) == "NH")) {
            echo " selected ";
        }
        echo ">New Hampshire</option>
                                            <option value=\"NJ\" ";
        // line 228
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 228) == "NJ")) {
            echo " selected ";
        }
        echo ">New Jersey</option>
                                            <option value=\"NM\" ";
        // line 229
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 229) == "NM")) {
            echo " selected ";
        }
        echo ">New Mexico</option>
                                            <option value=\"NY\" ";
        // line 230
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 230) == "NY")) {
            echo " selected ";
        }
        echo ">New York</option>
                                            <option value=\"NC\" ";
        // line 231
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 231) == "NC")) {
            echo " selected ";
        }
        echo ">North Carolina</option>
                                            <option value=\"ND\" ";
        // line 232
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 232) == "ND")) {
            echo " selected ";
        }
        echo ">North Dakota</option>
                                            <option value=\"OH\" ";
        // line 233
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 233) == "OH")) {
            echo " selected ";
        }
        echo ">Ohio</option>
                                            <option value=\"OK\" ";
        // line 234
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 234) == "OK")) {
            echo " selected ";
        }
        echo ">Oklahoma</option>
                                            <option value=\"OR\" ";
        // line 235
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 235) == "OR")) {
            echo " selected ";
        }
        echo ">Oregon</option>
                                            <option value=\"PA\" ";
        // line 236
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 236) == "PA")) {
            echo " selected ";
        }
        echo ">Pennsylvania</option>
                                            <option value=\"RI\" ";
        // line 237
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 237) == "RI")) {
            echo " selected ";
        }
        echo ">Rhode Island</option>
                                            <option value=\"SC\" ";
        // line 238
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 238) == "SC")) {
            echo " selected ";
        }
        echo ">South Carolina</option>
                                            <option value=\"SD\" ";
        // line 239
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 239) == "SD")) {
            echo " selected ";
        }
        echo ">South Dakota</option>
                                            <option value=\"TN\" ";
        // line 240
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 240) == "TN")) {
            echo " selected ";
        }
        echo ">Tennessee</option>
                                            <option value=\"TX\" ";
        // line 241
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 241) == "TX")) {
            echo " selected ";
        }
        echo ">Texas</option>
                                            <option value=\"UT\" ";
        // line 242
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 242) == "UT")) {
            echo " selected ";
        }
        echo ">Utah</option>
                                            <option value=\"VT\" ";
        // line 243
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 243) == "VT")) {
            echo " selected ";
        }
        echo ">Vermont</option>
                                            <option value=\"VA\" ";
        // line 244
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 244) == "VA")) {
            echo " selected ";
        }
        echo ">Virginia</option>
                                            <option value=\"WA\" ";
        // line 245
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 245) == "WA")) {
            echo " selected ";
        }
        echo ">Washington</option>
                                            <option value=\"WV\" ";
        // line 246
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 246) == "WV")) {
            echo " selected ";
        }
        echo ">West Virginia</option>
                                            <option value=\"WI\" ";
        // line 247
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 247) == "WI")) {
            echo " selected ";
        }
        echo ">Wisconsin</option>
                                            <option value=\"WY\" ";
        // line 248
        if ((twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "state", [], "any", false, false, false, 248) == "WY")) {
            echo " selected ";
        }
        echo ">Wyoming</option>
                                        </select>
                                    </div>
                                </div>
                                    
                                <div class=\"form-group\">
                                    <label for=\"zip\" class=\"col-md-3 col-xs-12 control-label\">Zip Code:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"number\" maxlenth=\"5\" name=\"zip\" class=\"form-control\" value=\"";
        // line 262
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "zip", [], "any", false, false, false, 262), "html", null, true);
        echo "\" placeholder=\"Zip\" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class=\"form-group\">
                                    <label for=\"social\" class=\"col-md-3 col-xs-12 control-label\">Last 4 of Social:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"text\" maxlength=\"4\" name=\"social\" class=\"form-control\" value=\"";
        // line 276
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "social", [], "any", false, false, false, 276), "html", null, true);
        echo "\" placeholder=\"Social\" />
                                        </div>
                                    </div>
                                </div>

                                <div class=\"form-group\">
                                    <label for=\"credit_company\" class=\"col-md-3 col-xs-12 control-label\">Credit Company:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"text\" name=\"credit_company\" class=\"form-control\" value=\"";
        // line 290
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "credit_company", [], "any", false, false, false, 290), "html", null, true);
        echo "\" placeholder=\"Credit Company\" />
                                        </div>
                                    </div>
                                </div>

                                <div class=\"form-group\">
                                    <label for=\"credit_company_user\" class=\"col-md-3 col-xs-12 control-label\">Credit Company User:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"text\" name=\"credit_company_user\" class=\"form-control\" value=\"";
        // line 304
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "credit_company_user", [], "any", false, false, false, 304), "html", null, true);
        echo "\" placeholder=\"Credit Company Username\" />
                                        </div>
                                    </div>
                                </div>

                                <div class=\"form-group\">
                                    <label for=\"credit_company_password\" class=\"col-md-3 col-xs-12 control-label\">Credit Company Password:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"text\" name=\"credit_company_password\" class=\"form-control\" value=\"";
        // line 318
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "credit_company_password", [], "any", false, false, false, 318), "html", null, true);
        echo "\" placeholder=\"Credit Company Password\" />
                                        </div>
                                    </div>
                                </div>

                                <div class=\"form-group\">
                                    <label for=\"credit_company_code\" class=\"col-md-3 col-xs-12 control-label\">Credit Company Code:</label>
                                    <div class=\"col-md-6 col-xs-12\">
                                        <div class=\"input-group\">
                                            <span class=\"input-group-addon\">
                                                <span class=\"fa fa-pencil\">
                                                    
                                                </span>
                                            </span>                                    
                                            <input type=\"text\" name=\"credit_company_code\" class=\"form-control\" value=\"";
        // line 332
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["parameters"] ?? null), "credit_company_code", [], "any", false, false, false, 332), "html", null, true);
        echo "\" placeholder=\"Credit Company Security Code\" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class=\"panel-footer\">
                            <button type=\"submit\" class=\"btn btn-primary\">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    ";
        // line 347
        $this->loadTemplate("footer-js.html.twig", "report/accounts-edit.html.twig", 347)->display($context);
        // line 348
        echo "</body>
</html>";
    }

    public function getTemplateName()
    {
        return "report/accounts-edit.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  715 => 348,  713 => 347,  695 => 332,  678 => 318,  661 => 304,  644 => 290,  627 => 276,  610 => 262,  591 => 248,  585 => 247,  579 => 246,  573 => 245,  567 => 244,  561 => 243,  555 => 242,  549 => 241,  543 => 240,  537 => 239,  531 => 238,  525 => 237,  519 => 236,  513 => 235,  507 => 234,  501 => 233,  495 => 232,  489 => 231,  483 => 230,  477 => 229,  471 => 228,  465 => 227,  459 => 226,  453 => 225,  447 => 224,  441 => 223,  435 => 222,  429 => 221,  423 => 220,  417 => 219,  411 => 218,  405 => 217,  399 => 216,  393 => 215,  387 => 214,  381 => 213,  375 => 212,  369 => 211,  363 => 210,  357 => 209,  351 => 208,  345 => 207,  339 => 206,  333 => 205,  327 => 204,  321 => 203,  315 => 202,  309 => 201,  303 => 200,  297 => 199,  291 => 198,  279 => 189,  262 => 175,  244 => 160,  227 => 146,  210 => 132,  193 => 118,  176 => 104,  161 => 92,  157 => 90,  150 => 85,  141 => 83,  137 => 82,  131 => 78,  128 => 77,  121 => 72,  112 => 70,  108 => 69,  102 => 65,  100 => 64,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "report/accounts-edit.html.twig", "/home/u819198500/domains/toptierfinancialsolutions.com/public_html/manage_program/templates/report/accounts-edit.html.twig");
    }
}
