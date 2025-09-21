<?php

namespace App\Service;

class ReportParser
{
    private function percent_to_img($percent)
    {
        $img = "credit-utilization-no-data.jpg";

        if ($percent >= 75) {
            $img = "credit-utilization-very-poor.jpg";
        } elseif ($percent >= 50) {
            $img = "credit-utilization-poor.jpg";
        } elseif ($percent >= 30) {
            $img = "credit-utilization-fair.jpg";
        } elseif ($percent >= 10) {
            $img = "credit-utilization-good.jpg";
        } elseif ($percent >= 0) {
            $img = "credit-utilization-excellent.jpg";
        }

        return $img;
    }

    private function remove_html_comments($content = '')
    {
        return preg_replace('/<!--(.|\s)*?-->/', '', $content);
    }

    private function strip_values($string)
    {
        $string = urlencode($string);
        return (str_replace("+", "", $string));
    }

    private function convert_value($value)
    {
        $value = $this->strip_values($value);
        if (strlen($value) > 0) {
            $value = str_replace(",", "", urlencode($value));
        } else {
            $value = 0;
        }

        return $value;
    }

    private function initialize(string $file)
    {
        $html = $this->remove_html_comments(file_get_contents($file));
        $html = str_replace(array("\r", "\n"), '', $html);
        $doc = new \DOMDocument();

        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        libxml_use_internal_errors(false);

        return $doc;
    }

    private function get_credit_limits(\DOMDocument $doc): array
    {
        $credit_info = array(
            "trans_union_balance"   =>  0,
            "experian_balance"      =>  0,
            "equifax_balance"       =>  0,
            "trans_union_limit"     =>  0,
            "experian_limit"        =>  0,
            "equifax_limit"         =>  0,
            "trans_union_percent_img" =>  "credit-utilization-no-data.jpg",
            "experian_percent_img"    =>  "credit-utilization-no-data.jpg",
            "equifax_percent_img"     =>  "credit-utilization-no-data.jpg",
            "trans_union_percent"   =>  0,
            "experian_percent"      =>  0,
            "equifax_percent"       =>  0,
            "total_balance"         =>  0,
            "total_limit"           =>  0,
            "total_percent"         =>  0,
        );

        $history = $doc->getElementsByTagName("address-history");

        foreach ($history as $item) {
            $tables = $item->getElementsByTagName("table");
            foreach ($tables as $table) {
                if ($table->getAttribute("class") == "crPrint ng-scope") {
                    $data_points = $table->getElementsByTagName("table");
                    foreach ($data_points as $data) {
                        $table_data = array(
                            "trans_union_account_type"      =>  null,
                            "trans_union_account_status"    =>  null,
                            "trans_union_balance"           =>  0,
                            "trans_union_limit"             =>  0,
                            "trans_union_percent"           =>  0,
                            "equifax_account_type"          =>  null,
                            "equifax_account_status"        =>  null,
                            "equifax_balance"               =>  0,
                            "equifax_limit"                 =>  0,
                            "equifax_percent"               =>  0,
                            "experian_account_type"         =>  null,
                            "experian_account_status"       =>  null,
                            "experian_balance"              =>  0,
                            "experian_limit"                =>  0,
                            "experian_percent"              =>  0,
                        );

                        if ($data->getAttribute("class") == "rpt_content_table rpt_content_header rpt_table4column ng-scope") {
                            $trs = $data->getElementsByTagName("tr");
                            $t = 0;
                            foreach ($trs as $tr) {
                                $tds = $tr->getElementsByTagName("td");
                                $i = 0;
                                foreach ($tds as $td) {
                                    if ($t == 2) {
                                        if ($i == 1) {
                                            $table_data["trans_union_account_type"] = $this->strip_values($td->nodeValue);
                                        } elseif ($i == 2) {
                                            $table_data["experian_account_type"] = $this->strip_values($td->nodeValue);
                                        } elseif ($i == 3) {
                                            $table_data["equifax_account_type"] = $this->strip_values($td->nodeValue);
                                        }
                                    }

                                    if ($t == 5) {
                                        if ($i == 1) {
                                            $table_data["trans_union_account_status"] = $this->strip_values($td->nodeValue);
                                        } elseif ($i == 2) {
                                            $table_data["experian_account_status"] = $this->strip_values($td->nodeValue);
                                        } elseif ($i == 3) {
                                            $table_data["equifax_account_status"] = $this->strip_values($td->nodeValue);
                                        }
                                    }

                                    if ($t == 8 && $i == 1) {
                                        $table_data["trans_union_balance"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                    } elseif ($t == 8 && $i == 2) {
                                        $table_data["experian_balance"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                    } elseif ($t == 8 && $i == 3) {
                                        $table_data["equifax_balance"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                    } elseif ($t == 11 && $i == 1) {
                                        $table_data["trans_union_limit"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                    } elseif ($t == 11 && $i == 2) {
                                        $table_data["experian_limit"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                    } elseif ($t == 11 && $i == 3) {
                                        $table_data["equifax_limit"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                    }

                                    $i++;
                                }
                                $t++;
                            }

                            if ($table_data['trans_union_account_type'] == "Revolving" && $table_data['trans_union_account_status'] == "Open") {
                                if (is_numeric($table_data['trans_union_balance'])) {
                                    $credit_info['trans_union_balance'] += $table_data['trans_union_balance'];
                                }
                                if (is_numeric($table_data['trans_union_limit'])) {
                                    $credit_info['trans_union_limit'] += $table_data['trans_union_limit'];
                                }
                                if ($credit_info['trans_union_limit'] > 0) {
                                    $credit_info['trans_union_percent'] = $credit_info['trans_union_balance'] / $credit_info['trans_union_limit'];
                                }
                            }

                            if ($table_data['equifax_account_type'] == "Revolving" && $table_data['equifax_account_status'] == "Open") {
                                if (is_numeric($table_data['equifax_balance'])) {
                                    $credit_info['equifax_balance'] += $table_data['equifax_balance'];
                                }
                                if (is_numeric($table_data['equifax_limit'])) {
                                    $credit_info['equifax_limit'] += $table_data['equifax_limit'];
                                }
                                if ($credit_info['equifax_limit'] > 0) {
                                    $credit_info['equifax_percent'] = $credit_info['equifax_balance'] / $credit_info['equifax_limit'];
                                }
                            }

                            if ($table_data['experian_account_type'] == "Revolving" && $table_data['experian_account_status'] == "Open") {
                                if (is_numeric($table_data['experian_balance'])) {
                                    $credit_info['experian_balance'] += $table_data['experian_balance'];
                                }
                                if (is_numeric($table_data['experian_limit'])) {
                                    $credit_info['experian_limit'] += $table_data['experian_limit'];
                                }
                                if ($credit_info['experian_limit'] > 0) {
                                    $credit_info['experian_percent'] = $credit_info['experian_balance'] / $credit_info['experian_limit'];
                                }
                            }
                        }
                    }
                }
            }
        }

        // Totals
        $credit_info['total_balance'] = ($credit_info["trans_union_balance"] + $credit_info["experian_balance"] + $credit_info["equifax_balance"]) / 3;
        $credit_info['total_limit'] = ($credit_info["trans_union_limit"] + $credit_info["experian_limit"] + $credit_info["equifax_limit"]) / 3;
        if ($credit_info['total_limit'] > 0) {
            $credit_info['total_percent'] = $credit_info['total_balance'] / $credit_info['total_limit'];
        }

        // Format
        $credit_info["trans_union_balance"] = number_format($credit_info["trans_union_balance"], 2);
        $credit_info["trans_union_limit"] = number_format($credit_info["trans_union_limit"], 2);
        $credit_info["trans_union_percent"] = round($credit_info["trans_union_percent"] * 100, 2);
        $credit_info["trans_union_percent_img"] = $this->percent_to_img($credit_info["trans_union_percent"]);

        $credit_info["equifax_balance"] = number_format($credit_info["equifax_balance"], 2);
        $credit_info["equifax_limit"] = number_format($credit_info["equifax_limit"], 2);
        $credit_info["equifax_percent"] = round($credit_info["equifax_percent"] * 100, 2);
        $credit_info["equifax_percent_img"] = $this->percent_to_img($credit_info["equifax_percent"]);

        $credit_info["experian_balance"] = number_format($credit_info["experian_balance"], 2);
        $credit_info["experian_limit"] = number_format($credit_info["experian_limit"], 2);
        $credit_info["experian_percent"] = round($credit_info["experian_percent"] * 100, 2);
        $credit_info["experian_percent_img"] = $this->percent_to_img($credit_info["experian_percent"]);

        $credit_info['total_balance'] = number_format($credit_info['total_balance'], 2);
        $credit_info['total_limit'] = number_format($credit_info['total_limit'], 2);
        $credit_info['total_percent'] = round($credit_info['total_percent'] * 100, 2);

        return $credit_info;
    }

    private function get_public_records(\DOMDocument $doc): array
    {
        $public_records = array(
            "total"     =>  0,
            "records"   =>  array(),
        );

        $records = $doc->getElementById("PublicInformation");
        if (!$records) {
            return $public_records;
        }

        $ngs = $records->getElementsByTagName("ng");
        foreach ($ngs as $ng) {
            $divs = $ng->getElementsByTagName("div");
            foreach ($divs as $div) {
                if ($div->getAttribute("class") != "ng-hide" || $div->getAttribute("class") != "sub_header") {
                    $tables = $div->getElementsByTagName("table");
                    foreach ($tables as $table) {
                        $type = "";
                        $status = "";
                        $experian_filed = "";
                        $equifax_filed = "";
                        $trans_unions_filed = "";

                        if ($table->getAttribute("class") == "rpt_content_table rpt_content_header rpt_table4column") {
                            $t = 0;
                            $row = $table->getElementsByTagName("tr");
                            foreach ($row as $tr) {
                                $d = 0;
                                $tds = $tr->getElementsByTagName("td");
                                foreach ($tds as $td) {
                                    if ($t == 1 && ($d == 1 || $d == 2 || $d == 3)) {
                                        $type = $td->nodeValue;
                                    }
                                    if ($t == 2 && ($d == 1 || $d == 2 || $d == 3)) {
                                        $status = $td->nodeValue;
                                    }
                                    if ($t == 3) {
                                        if ($d == 1) {
                                            $trans_unions_filed = $td->nodeValue;
                                        } elseif ($d == 2) {
                                            $experian_filed = $td->nodeValue;
                                        } elseif ($d == 3) {
                                            $equifax_filed = $td->nodeValue;
                                        }
                                    }
                                    $d++;
                                }
                                $t++;
                            }
                        }

                        if (strlen($this->strip_values($type)) > 0) {
                            $public_records['total']++;
                            $public_records['records'][] = array(
                                "type"  =>  $type,
                                "trans_union_files" =>  $trans_unions_filed,
                                "experian_filed"    =>  $experian_filed,
                                "equifax_filed"     =>  $equifax_filed,
                                "status"            =>  $status,
                            );
                        }
                    }
                }
            }
        }
        return $public_records;
    }

    private function get_inquiries(\DOMDocument $doc): array
    {
        $info = array(
            "total"     =>  0,
            "accounts"  =>  array(),
        );

        $inquiries = $doc->getElementById("Inquiries");
        if (!$inquiries) {
            return $info;
        }

        $tables = $inquiries->getElementsByTagName("table");
        foreach ($tables as $table) {
            if ($table->getAttribute("class") == "rpt_content_table rpt_content_header rpt_content_contacts ng-scope") {
                $trs = $table->getElementsByTagName("tr");
                $t = 0;
                foreach ($trs as $tr) {
                    if ($t > 0) {
                        $tds = $tr->getElementsByTagName("td");
                        $data = array();
                        foreach ($tds as $td) {
                            $data[] = $td->nodeValue;
                        }
                        $info['total']++;
                        $info['accounts'][] = $data;
                    }
                    $t++;
                }
            }
        }
        return $info;
    }

    private function get_degrogatory_accounts(\DOMDocument $doc): array
    {
        $derogatory_accounts = array(
            "total"     =>  0,
            "accounts"  =>  array()
        );

        $history = $doc->getElementsByTagName("address-history");
        foreach ($history as $item) {
            $tables = $item->getElementsByTagName("table");
            foreach ($tables as $table) {
                if ($table->getAttribute("class") == "crPrint ng-scope") {
                    $table_data = array(
                        "account"                       =>  null,
                        "unique_status"                 =>  null,
                        "trans_union_account_status"    =>  null,
                        "trans_union_account_date"      =>  null,
                        "trans_union_payment_status"    =>  null,
                        "experian_account_status"       =>  null,
                        "experian_account_date"         =>  null,
                        "experian_payment_status"       =>  null,
                        "equifax_account_status"        =>  null,
                        "equifax_account_date"          =>  null,
                        "equifax_payment_status"        =>  null,
                    );

                    $headers = $table->getElementsByTagName("div");
                    foreach ($headers as $h) {
                        if ($h->getAttribute("class") == "sub_header ng-binding ng-scope") $table_data["account"] = $h->nodeValue;
                    }

                    $data_points = $table->getElementsByTagName("table");
                    foreach ($data_points as $data) {
                        if ($data->getAttribute("class") == "rpt_content_table rpt_content_header rpt_table4column ng-scope") {
                            $trs = $data->getElementsByTagName("tr");
                            $t = 0;
                            foreach ($trs as $tr) {
                                $tds = $tr->getElementsByTagName("td");
                                $i = 0;
                                foreach ($tds as $td) {
                                    if ($t == 5 && $i == 1) {
                                        $table_data["trans_union_account_status"] = $this->strip_values($td->nodeValue);
                                    } elseif ($t == 5 && $i == 2) {
                                        $table_data["experian_account_status"] = $this->strip_values($td->nodeValue);
                                    } elseif ($t == 5 && $i == 3) {
                                        $table_data["equifax_account_status"] = $this->strip_values($td->nodeValue);
                                    } elseif ($t == 13 && $i == 1) {
                                        $table_data["trans_union_payment_status"] = $td->nodeValue;
                                    } elseif ($t == 13 && $i == 2) {
                                        $table_data["experian_payment_status"] = $td->nodeValue;
                                    } elseif ($t == 13 && $i == 3) {
                                        $table_data["equifax_payment_status"] = $td->nodeValue;
                                    } elseif ($t == 7 && $i == 1) {
                                        $table_data["trans_union_account_date"] = $td->nodeValue;
                                    } elseif ($t == 7 && $i == 2) {
                                        $table_data["experian_account_date"] = $td->nodeValue;
                                    } elseif ($t == 7 && $i == 3) {
                                        $table_data["equifax_account_date"] = $td->nodeValue;
                                    }
                                    $i++;
                                }
                                $t++;
                            }
                        }
                    }

                    $add = false;
                    if ($table_data['trans_union_account_status'] == "Derogatory" || stripos($table_data['trans_union_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['trans_union_payment_status'], "Late 30") !== false || stripos($table_data['trans_union_payment_status'], "Late 60") !== false || stripos($table_data['trans_union_payment_status'], "Late 90") !== false || stripos($table_data['trans_union_payment_status'], "Late 120") !== false || stripos($table_data['trans_union_payment_status'], "Late 150") !== false || stripos($table_data['trans_union_payment_status'], "Late 180") !== false) {
                        $derogatory_accounts["total"]++;
                        $table_data["unique_status"] = implode(", ", array_unique(array($table_data["trans_union_payment_status"], $table_data["experian_payment_status"], $table_data["equifax_payment_status"])));
                        $add = true;
                    }

                    if ($table_data['experian_account_status'] == "Derogatory" || stripos($table_data['experian_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['experian_payment_status'], "Late 30") !== false || stripos($table_data['experian_payment_status'], "Late 60") !== false || stripos($table_data['experian_payment_status'], "Late 90") !== false || stripos($table_data['experian_payment_status'], "Late 120") !== false || stripos($table_data['experian_payment_status'], "Late 150") !== false || stripos($table_data['experian_payment_status'], "Late 180") !== false) {
                        $derogatory_accounts["total"]++;
                        $table_data["unique_status"] = implode(", ", array_unique(array($table_data["trans_union_payment_status"], $table_data["experian_payment_status"], $table_data["equifax_payment_status"])));
                        $add = true;
                    }

                    if ($table_data['equifax_account_status'] == "Derogatory" || stripos($table_data['equifax_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['equifax_payment_status'], "Late 30") !== false || stripos($table_data['equifax_payment_status'], "Late 60") !== false || stripos($table_data['equifax_payment_status'], "Late 90") !== false || stripos($table_data['equifax_payment_status'], "Late 120") !== false || stripos($table_data['equifax_payment_status'], "Late 150") !== false || stripos($table_data['equifax_payment_status'], "Late 180") !== false) {
                        $derogatory_accounts["total"]++;
                        $table_data["unique_status"] = implode(", ", array_unique(array($table_data["trans_union_payment_status"], $table_data["experian_payment_status"], $table_data["equifax_payment_status"])));
                        $add = true;
                    }

                    if ($add === true) $derogatory_accounts['accounts'][] = $table_data;

                    if (($table_data['trans_union_account_status'] == "Derogatory" || $table_data['experian_account_status'] == "Derogatory" || $table_data['equifax_account_status'] == "Derogatory" || stripos($table_data['trans_union_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['experian_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['equifax_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['trans_union_payment_status'], "Late 30") !== false || stripos($table_data['trans_union_payment_status'], "Late 60") !== false || stripos($table_data['trans_union_payment_status'], "Late 90") !== false || stripos($table_data['trans_union_payment_status'], "Late 120") !== false || stripos($table_data['trans_union_payment_status'], "Late 150") !== false || stripos($table_data['trans_union_payment_status'], "Late 180") !== false || stripos($table_data['experian_payment_status'], "Late 30") !== false || stripos($table_data['experian_payment_status'], "Late 60") !== false || stripos($table_data['experian_payment_status'], "Late 90") !== false || stripos($table_data['experian_payment_status'], "Late 120") !== false || stripos($table_data['experian_payment_status'], "Late 150") !== false || stripos($table_data['experian_payment_status'], "Late 180") !== false || stripos($table_data['equifax_payment_status'], "Late 30") !== false || stripos($table_data['equifax_payment_status'], "Late 60") !== false || stripos($table_data['equifax_payment_status'], "Late 90") !== false || stripos($table_data['equifax_payment_status'], "Late 120") !== false || stripos($table_data['equifax_payment_status'], "Late 150") !== false || stripos($table_data['equifax_payment_status'], "Late 180") !== false) && $add === false) {

                        // Clear statuses for bureaus that are not derogatory
                        if (!($table_data['trans_union_account_status'] == "Derogatory" || stripos($table_data['trans_union_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['trans_union_payment_status'], "Late 30") !== false || stripos($table_data['trans_union_payment_status'], "Late 60") !== false || stripos($table_data['trans_union_payment_status'], "Late 90") !== false || stripos($table_data['trans_union_payment_status'], "Late 120") !== false || stripos($table_data['trans_union_payment_status'], "Late 150") !== false || stripos($table_data['trans_union_payment_status'], "Late 180") !== false)) {
                            $table_data['trans_union_payment_status'] = "";
                            $table_data['trans_union_account_date'] = "";
                        }

                        if (!($table_data['experian_account_status'] == "Derogatory" || stripos($table_data['experian_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['experian_payment_status'], "Late 30") !== false || stripos($table_data['experian_payment_status'], "Late 60") !== false || stripos($table_data['experian_payment_status'], "Late 90") !== false || stripos($table_data['experian_payment_status'], "Late 120") !== false || stripos($table_data['experian_payment_status'], "Late 150") !== false || stripos($table_data['experian_payment_status'], "Late 180") !== false)) {
                            $table_data['experian_payment_status'] = "";
                            $table_data['experian_account_date'] = "";
                        }

                        if (!($table_data['equifax_account_status'] == "Derogatory" || stripos($table_data['equifax_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['equifax_payment_status'], "Late 30") !== false || stripos($table_data['equifax_payment_status'], "Late 60") !== false || stripos($table_data['equifax_payment_status'], "Late 90") !== false || stripos($table_data['equifax_payment_status'], "Late 120") !== false || stripos($table_data['equifax_payment_status'], "Late 150") !== false || stripos($table_data['equifax_payment_status'], "Late 180") !== false)) {
                            $table_data['equifax_payment_status'] = "";
                            $table_data['equifax_account_date'] = "";
                        }

                        $table_data["unique_status"] = implode(", ", array_unique(array($table_data["trans_union_payment_status"], $table_data["experian_payment_status"], $table_data["equifax_payment_status"])));
                        $derogatory_accounts['accounts'][] = $table_data;
                    }
                }
            }
        }

        return $derogatory_accounts;
    }

    private function get_personal_info(\DOMDocument $doc): \stdClass
    {
        $tables = $doc->getElementsByTagName("table");
        $response = "";
        $x = 0;
        $values = array();

        foreach ($tables as $table) {
            $class = $table->getAttribute('class');

            if (stripos($class, "rpt_content_table") !== false) {
                $tds = $table->getElementsByTagName("td");
                foreach ($tds as $td) {
                    $response .= "{$x} - {$td->nodeValue}</br>";
                    $values[] = $td->nodeValue;
                    $x++;
                }
            }
        }

        $client_data = new \stdClass;

        // Trans Union
        $client_data->trans_union = array();
        $client_data->trans_union['report_data'] = $values[7] ?? null;
        $client_data->trans_union['name'] = $values[11] ?? null;
        $client_data->trans_union['also_known_as'] = $values[15] ?? null;
        $client_data->trans_union['former_name'] = $values[19] ?? null;
        $client_data->trans_union['date_of_birth'] = $values[23] ?? null;
        $client_data->trans_union['current_address'] = $values[27] ?? null;
        $client_data->trans_union['previous_address'] = $values[31] ?? null;
        $client_data->trans_union['employers'] = $values[35] ?? null;
        $client_data->trans_union['credit_score'] = $values[39] ?? null;
        $client_data->trans_union['lending_rank'] = $values[43] ?? null;
        $client_data->trans_union['score_scale'] = $values[47] ?? null;
        $client_data->trans_union['total_accounts'] = $values[58] ?? null;
        $client_data->trans_union['open_accounts'] = $values[62] ?? null;
        $client_data->trans_union['closed_accounts'] = $values[66] ?? null;
        $client_data->trans_union['delinquent'] = $values[70] ?? null;
        $client_data->trans_union['derogatory'] = $values[74] ?? null;
        $client_data->trans_union['collection'] = $values[78] ?? null;
        $client_data->trans_union['balances'] = $values[82] ?? null;
        $client_data->trans_union['payments'] = $values[86] ?? null;
        $client_data->trans_union['public_records'] = $values[90] ?? null;
        $client_data->trans_union['inquiries'] = $values[94] ?? null;

        // Experian
        $client_data->experian = array();
        $client_data->experian['report_data'] = $values[8] ?? null;
        $client_data->experian['name'] = $values[12] ?? null;
        $client_data->experian['also_known_as'] = $values[16] ?? null;
        $client_data->experian['former_name'] = $values[20] ?? null;
        $client_data->experian['date_of_birth'] = $values[24] ?? null;
        $client_data->experian['current_address'] = $values[28] ?? null;
        $client_data->experian['previous_address'] = $values[32] ?? null;
        $client_data->experian['employers'] = $values[36] ?? null;
        $client_data->experian['credit_score'] = $values[40] ?? null;
        $client_data->experian['lending_rank'] = $values[45] ?? null;
        $client_data->experian['score_scale'] = $values[48] ?? null;
        $client_data->experian['total_accounts'] = $values[59] ?? null;
        $client_data->experian['open_accounts'] = $values[63] ?? null;
        $client_data->experian['closed_accounts'] = $values[67] ?? null;
        $client_data->experian['delinquent'] = $values[71] ?? null;
        $client_data->experian['derogatory'] = $values[75] ?? null;
        $client_data->experian['collection'] = $values[79] ?? null;
        $client_data->experian['balances'] = $values[83] ?? null;
        $client_data->experian['payments'] = $values[87] ?? null;
        $client_data->experian['public_records'] = $values[91] ?? null;
        $client_data->experian['inquiries'] = $values[95] ?? null;

        // Equifax
        $client_data->equifax = array();
        $client_data->equifax['report_data'] = $values[9] ?? null;
        $client_data->equifax['name'] = $values[13] ?? null;
        $client_data->equifax['also_known_as'] = $values[16] ?? null;
        $client_data->equifax['former_name'] = $values[21] ?? null;
        $client_data->equifax['date_of_birth'] = $values[25] ?? null;
        $client_data->equifax['current_address'] = $values[29] ?? null;
        $client_data->equifax['previous_address'] = $values[33] ?? null;
        $client_data->equifax['employers'] = $values[37] ?? null;
        $client_data->equifax['credit_score'] = $values[41] ?? null;
        $client_data->equifax['lending_rank'] = $values[45] ?? null;
        $client_data->equifax['score_scale'] = $values[49] ?? null;
        $client_data->equifax['total_accounts'] = $values[60] ?? null;
        $client_data->equifax['open_accounts'] = $values[64] ?? null;
        $client_data->equifax['closed_accounts'] = $values[68] ?? null;
        $client_data->equifax['delinquent'] = $values[72] ?? null;
        $client_data->equifax['derogatory'] = $values[76] ?? null;
        $client_data->equifax['collection'] = $values[80] ?? null;
        $client_data->equifax['balances'] = $values[84] ?? null;
        $client_data->equifax['payments'] = $values[88] ?? null;
        $client_data->equifax['public_records'] = $values[92] ?? null;
        $client_data->equifax['inquiries'] = $values[96] ?? null;

        return $client_data;
    }

    public function loadReportData(string $file): array
    {
        $doc = $this->initialize($file);

        $client_data = $this->get_personal_info($doc);
        $derogatory_accounts = $this->get_degrogatory_accounts($doc);
        $inquiry_accounts = $this->get_inquiries($doc);
        $public_records = $this->get_public_records($doc);
        $credit_info = $this->get_credit_limits($doc);

        return [
            'client_data' => $client_data,
            'derogatory_accounts' => $derogatory_accounts,
            'inquiry_accounts' => $inquiry_accounts,
            'public_records' => $public_records,
            'credit_info' => $credit_info,
        ];
    }
}

?>