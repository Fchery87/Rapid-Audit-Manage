<?php



    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

    class ParserController extends AbstractController {

        private $file_name;
        private $client_data;
        private $derogatory_accounts = array();
        private $directory = "../misc";

        public function __init() {

            if(isset($_GET['file'])) {
                $this->file_name = urldecode($_GET['file']);

                if(file_exists("{$this->directory}/{$this->file_name}") && !is_dir("{$this->directory}/{$this->file_name}")) {
                    $account_details = $this->get_account_details($this->file_name);
                } else {
                    throw new NotFoundHttpException('File not found!');
                }
            } else {
                throw new NotFoundHttpException('File not found!');
            }


            $doc = $this->initialize("{$this->directory}/{$this->file_name}");
            $this->get_personal_info($doc);
            $this->derogatory_accounts = $this->get_degrogatory_accounts($doc);
            $inquiry_accounts = $this->get_inquiries($doc);
            $public_records = $this->get_public_records($doc);
            $credit_info = $this->get_credit_limits($doc);

            return $this->render('credit-report.html.twig', [
                "first_name"    =>  $account_details['first_name'],
                "last_name"     =>  $account_details['last_name'],
                'name' => $this->client_data->trans_union['name'],
                'report_date' => $this->client_data->trans_union['report_data'],
                "transunion_credit_score" => $this->client_data->trans_union['credit_score'],
                "equifax_credit_score" => $this->client_data->equifax['credit_score'],
                "experian_credit_score" => $this->client_data->experian['credit_score'],
                "equifax_delinquent" => $this->client_data->equifax['delinquent'],
                "experian_delinquent" => $this->client_data->experian['delinquent'],
                "transunion_delinquent" => $this->client_data->trans_union['delinquent'],
                "equifax_derogatory" =>  $this->client_data->equifax['derogatory'],
                "experian_derogatory" =>  $this->client_data->experian['derogatory'],
                "transunion_derogatory" =>  $this->client_data->trans_union['derogatory'],
                "equifax_collection" =>  $this->client_data->equifax['collection'],
                "experian_collection" =>  $this->client_data->experian['collection'],
                "transunion_collection" =>  $this->client_data->trans_union['collection'],
                "equifax_public_records" =>  $this->client_data->equifax['public_records'],
                "experian_public_records" =>  $this->client_data->experian['public_records'],
                "transunion_public_records" =>  $this->client_data->trans_union['public_records'],  
                "equifax_inquiries" =>  $this->client_data->equifax['inquiries'],
                "experian_inquiries" =>  $this->client_data->experian['inquiries'],
                "transunion_inquiries" =>  $this->client_data->trans_union['inquiries'],      
                "derogatory_accounts" => $this->derogatory_accounts['accounts'],
                "derogatory_accounts_total" =>  $this->derogatory_accounts['total'],
                "inquiry_accounts"  =>  $inquiry_accounts["accounts"],
                "inquiry_total" =>  $inquiry_accounts["total"],
                "equifax_open_accounts" =>  $this->client_data->equifax['open_accounts'],
                "transunion_open_accounts" =>  $this->client_data->trans_union['open_accounts'],
                "experian_open_accounts" =>  $this->client_data->experian['open_accounts'],
                "equifax_total_accounts" =>  $this->client_data->equifax['total_accounts'],
                "transunion_total_accounts" =>  $this->client_data->trans_union['total_accounts'],
                "experian_total_accounts" =>  $this->client_data->experian['total_accounts'],
                "equifax_closed_accounts" =>  $this->client_data->equifax['closed_accounts'],
                "transunion_closed_accounts" =>  $this->client_data->trans_union['closed_accounts'],
                "experian_closed_accounts" =>  $this->client_data->experian['closed_accounts'],
                "equifax_balances" =>  $this->client_data->equifax['balances'],
                "transunion_balances" =>  $this->client_data->trans_union['balances'],
                "experian_balances" =>  $this->client_data->experian['balances'],
                "equifax_payments" =>  $this->client_data->equifax['payments'],
                "transunion_payments" =>  $this->client_data->trans_union['payments'],
                "experian_payments" =>  $this->client_data->experian['payments'],
                "public_records"    =>  $public_records['records'],
                "public_records_total"  =>  $public_records['total'],
                "credit_info"   => $credit_info,
            ]);

        }

        public function __init_raw() {


            $doc = $this->initialize($this->file_name);
            $values = $this->get_degrogatory_accounts($doc);
            //$response = "";

            //foreach($values as $key => $value) {
            //    $response .= "{$key} - {$value}</br>";
            //}

            $response = new Response();
            $response->setContent(json_encode($values['info']));
            $response->headers->set('Content-Type', 'application/json');

            return $response;

        }

        private function get_account_details($file = null) {
            if($file) {
                $em = $this->getDoctrine()->getManager();
                $query = "SELECT first_name, last_name FROM accounts a INNER JOIN account_files f WHERE f.filename = :filename AND f.aid = a.aid";
                $statement = $em->getConnection()->prepare($query);
                $statement->bindValue("filename", $file);
                $statement->execute();
                $rows = $statement->fetchAll();
            }

            return($rows[0]);
        }

        private function percent_to_img($percent) {
            $img = "credit-utilization-no-data.jpg";

            if($percent >= 75) {
                $img = "credit-utilization-very-poor.jpg";
            } elseif($percent >= 50) {
                $img = "credit-utilization-poor.jpg";
            } elseif($percent >= 30) {
                $img = "credit-utilization-fair.jpg";
            } elseif($percent >= 10) {
                $img = "credit-utilization-good.jpg";
            } elseif($percent >= 0) {
                $img = "credit-utilization-excellent.jpg";
            }

            return $img;
        }

        private function remove_html_comments($content = '') {
            return preg_replace('/<!--(.|\s)*?-->/', '', $content);
        }

        private function strip_values($string) {
            $string = urlencode($string);
            return(str_replace("+","",$string));
        }

        private function convert_value($value) {
            $value = $this->strip_values($value);
            if(strlen($value) > 0) {
                $value = str_replace(",", "", urlencode($value));
            } else {
                $value = 0;
            }

            return $value;
        }

        private function initialize($file) {

            
            $html = $this->remove_html_comments(file_get_contents($file));
            $html = str_replace(array("\r", "\n"), '', $html);
            $doc = new \DOMDocument();

            libxml_use_internal_errors(true);
            $doc->loadHTML($html);
            libxml_use_internal_errors(false);

            return $doc;

            
        }

        private function get_credit_limits($doc) {


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

            $x = 0;

            foreach($history as $item) {
                $tables = $item->getElementsByTagName("table");

                $j = 0;



                


                foreach($tables as $table) {



                    if($table->getAttribute("class") == "crPrint ng-scope") {




                
                        $data_points = $table->getElementsByTagName("table");



                        foreach($data_points as $data) {
                        

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
                            

                            if($data->getAttribute("class") == "rpt_content_table rpt_content_header rpt_table4column ng-scope") {
                                // Get TR
                                $trs = $data->getElementsByTagName("tr");

                                $t = 0;
                                



                                foreach($trs as $tr) {
                                    // Get TD

                                    $tds = $tr->getElementsByTagName("td");

                                    $i = 0;
                                    
                                    foreach($tds as $td) {

                                        if($t == 2) {
                                            if($i == 1) {
                                                $table_data["trans_union_account_type"] = $this->strip_values($td->nodeValue);
                                            } elseif($i == 2) {
                                                $table_data["experian_account_type"] = $this->strip_values($td->nodeValue);
                                            } elseif($i == 3) {
                                                $table_data["equifax_account_type"] = $this->strip_values($td->nodeValue);
                                            }

                                        }


                                        if($t == 5) {
                                            if($i == 1) {
                                                $table_data["trans_union_account_status"] = $this->strip_values($td->nodeValue);
                                            } elseif($i == 2) {
                                                $table_data["experian_account_status"] = $this->strip_values($td->nodeValue);
                                            } elseif($i == 3) {
                                                $table_data["equifax_account_status"] = $this->strip_values($td->nodeValue);
                                            }

                                        }
                                        
                                        if($t == 8 && $i == 1) {
                                            $table_data["trans_union_balance"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                        } elseif($t == 8 && $i == 2) {
                                            $table_data["experian_balance"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                        } elseif($t == 8 && $i == 3) {
                                            $table_data["equifax_balance"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                        } elseif($t == 11 && $i == 1) {
                                            $table_data["trans_union_limit"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                        } elseif($t == 11 && $i == 2) {
                                            $table_data["experian_limit"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                        } elseif($t == 11 && $i == 3) {
                                            $table_data["equifax_limit"] = filter_var($td->nodeValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                        }
                                            
                                        

                                        $i++;
                                    }   


                                    $t++;
                                }


                                if($table_data['trans_union_account_type'] == "Revolving" && $table_data['trans_union_account_status'] == "Open") {
                                    if(is_numeric($table_data['trans_union_balance'])) {
                                        $credit_info['trans_union_balance'] += $table_data['trans_union_balance'];
                                    }

                                    if(is_numeric($table_data['trans_union_limit'])) {
                                        $credit_info['trans_union_limit'] += $table_data['trans_union_limit'];
                                    }

                                    if($credit_info['trans_union_limit'] > 0) {
                                        $credit_info['trans_union_percent'] = $credit_info['trans_union_balance'] / $credit_info['trans_union_limit'];
                                    }
                                }

                                if($table_data['equifax_account_type'] == "Revolving" && $table_data['equifax_account_status'] == "Open") {
                                    if(is_numeric($table_data['equifax_balance'])) {
                                        $credit_info['equifax_balance'] += $table_data['equifax_balance'];
                                    }

                                    if(is_numeric($table_data['equifax_limit'])) {
                                        $credit_info['equifax_limit'] += $table_data['equifax_limit'];
                                    }

                                    if($credit_info['equifax_limit'] > 0) {
                                        $credit_info['equifax_percent'] = $credit_info['equifax_balance'] / $credit_info['equifax_limit'];
                                    }
                                }

                                if($table_data['experian_account_type'] == "Revolving" && $table_data['experian_account_status'] == "Open") {
                                    if(is_numeric($table_data['experian_balance'])) {
                                        $credit_info['experian_balance'] += $table_data['experian_balance'];
                                    }

                                    if(is_numeric($table_data['experian_limit'])) {
                                        $credit_info['experian_limit'] += $table_data['experian_limit'];
                                    }

                                    if($credit_info['experian_limit'] > 0) {
                                        $credit_info['experian_percent'] = $credit_info['experian_balance'] / $credit_info['experian_limit'];
                                    }
                                }

                            }
                            $j++;


                        }

                        $x++;



                    } 



                }



            }

            // Totals
            $credit_info['total_balance'] = ($credit_info["trans_union_balance"] + $credit_info["experian_balance"] + $credit_info["equifax_balance"]) / 3;
            $credit_info['total_limit'] = ($credit_info["trans_union_limit"] + $credit_info["experian_limit"] + $credit_info["equifax_limit"]) / 3;
            if($credit_info['total_limit'] > 0) {
                $credit_info['total_percent'] = $credit_info['total_balance'] / $credit_info['total_limit'];
            }

            // Format
            $credit_info["trans_union_balance"] = number_format($credit_info["trans_union_balance"],2);
            $credit_info["trans_union_limit"] = number_format($credit_info["trans_union_limit"],2);
            $credit_info["trans_union_percent"] = round($credit_info["trans_union_percent"] * 100, 2);
            $credit_info["trans_union_percent_img"] = $this->percent_to_img($credit_info["trans_union_percent"]);

            $credit_info["equifax_balance"] = number_format($credit_info["equifax_balance"],2);
            $credit_info["equifax_limit"] = number_format($credit_info["equifax_limit"],2);
            $credit_info["equifax_percent"] = round($credit_info["equifax_percent"] * 100, 2);
            $credit_info["equifax_percent_img"] = $this->percent_to_img($credit_info["equifax_percent"]);

            $credit_info["experian_balance"] = number_format($credit_info["experian_balance"],2);
            $credit_info["experian_limit"] = number_format($credit_info["experian_limit"],2);
            $credit_info["experian_percent"] = round($credit_info["experian_percent"] * 100, 2);
            $credit_info["experian_percent_img"] = $this->percent_to_img($credit_info["experian_percent"]);

            $credit_info['total_balance'] = number_format($credit_info['total_balance'], 2);
            $credit_info['total_limit'] = number_format($credit_info['total_limit'], 2);
            $credit_info['total_percent'] = round($credit_info['total_percent'] * 100,2);

            return $credit_info;



        }

        private function get_public_records($doc) {
            $values = array();
            $public_records = array(
                "total"     =>  0,
                "records"   =>  array(),
            );

            $records = $doc->getElementById("PublicInformation");

            $ngs = $records->getElementsByTagName("ng");

            foreach($ngs as $ng) {
                $divs = $ng->getElementsByTagName("div");

                foreach($divs as $div) {
                    if($div->getAttribute("class") != "ng-hide" || $div->getAttribute("class") != "sub_header") {
                        $tables = $div->getElementsByTagName("table");

                        foreach($tables as $table) {
                            $type = "";
                            $status = "";
                            $experian_filed = "";
                            $equifax_filed = "";
                            $trans_unions_filed = "";
            
                            if($table->getAttribute("class") == "rpt_content_table rpt_content_header rpt_table4column") {
            
                                $t = 0;
            
                                $row = $table->getElementsByTagName("tr");
                     
                                foreach($row as $tr) {
            
                                    $d = 0;
                                    $tds = $tr->getElementsByTagName("td");
                                    foreach($tds as $td) {
            
                                        
            
                                        if($t == 1 && ($d == 1 || $d ==2 || $d == 3)) {
                                            $type = $td->nodeValue;
                                            
                                        }
                                        if($t == 2 && ($d == 1 || $d == 2 || $d == 3)) {
                                            $status = $td->nodeValue;
                                        }
            
                                        if($t == 3) {
            
                                            if($d == 1) {
                                                $trans_unions_filed = $td->nodeValue;
                                            } elseif($d == 2) {
                                                $experian_filed = $td->nodeValue;
                                            } elseif($d == 3) {
                                                $equifax_filed = $td->nodeValue;
                                            }
                                            
                                        }
                                        $d++;
                                    }
            
                                    $t++;
                                }
                                
                            } 

                            if(strlen($this->strip_values($type)) > 0) {
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

        private function get_inquiries($doc) {

            $info = array(
                "total"     =>  0,
                "accounts"  =>  array(),
            );

            $inquiry_accounts = array();

            $inquiries = $doc->getElementById("Inquiries");
 
            $tables = $inquiries->getElementsByTagName("table");

            foreach($tables as $table) {
                if($table->getAttribute("class") == "rpt_content_table rpt_content_header rpt_content_contacts ng-scope") {
                    $trs = $table->getElementsByTagName("tr");
                    $t = 0;
                    
                    foreach($trs as $tr) {

                        if($t > 0) {
                            $tds = $tr->getElementsByTagName("td");

                            $x = 0;

                            $data = array();

                            foreach($tds as $td) {

                                $data[] = $td->nodeValue;

                                $x++;

        
                                
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

        private function get_degrogatory_accounts($doc) {

            $derogatory_accounts = array(
                "total"     =>  0,
                "accounts"  =>  array()
            );

            $history = $doc->getElementsByTagName("address-history");

            foreach($history as $item) {
                $tables = $item->getElementsByTagName("table");

                foreach($tables as $table) {
                    if($table->getAttribute("class") == "crPrint ng-scope") {

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

                        foreach($headers as $h) {
                            if($h->getAttribute("class") == "sub_header ng-binding ng-scope") $table_data["account"] = $h->nodeValue;
                        }

                        $data_points = $table->getElementsByTagName("table");

                        foreach($data_points as $data) {


                            
                            if($data->getAttribute("class") == "rpt_content_table rpt_content_header rpt_table4column ng-scope") {

                                // Get TR
                                $trs = $data->getElementsByTagName("tr");

                                $t = 0;

                                foreach($trs as $tr) {
                                    // Get TD

                                    $tds = $tr->getElementsByTagName("td");

                                    $i = 0;

                                    
                                    foreach($tds as $td) {

                                        if ($t == 5 && $i == 1) {
                                            $table_data["trans_union_account_status"] = $this->strip_values($td->nodeValue);
                                        } elseif ($t == 5 && $i == 2) {
                                            $table_data["experian_account_status"] = $this->strip_values($td->nodeValue);
                                        } elseif ($t == 5 && $i == 3) {
                                            $table_data["equifax_account_status"] = $this->strip_values($td->nodeValue);
                                        } elseif($t == 13 && $i == 1) {
                                            $table_data["trans_union_payment_status"] = $td->nodeValue;
                                        } elseif($t == 13 && $i == 2) {
                                            $table_data["experian_payment_status"] = $td->nodeValue;
                                        } elseif($t == 13 && $i == 3) {
                                            $table_data["equifax_payment_status"] = $td->nodeValue;
                                        } elseif($t == 7 && $i == 1) {
                                            $table_data["trans_union_account_date"] = $td->nodeValue;
                                        } elseif($t == 7 && $i == 2) {
                                            $table_data["experian_account_date"] = $td->nodeValue;
                                        } elseif($t == 7 && $i == 3) {
                                            $table_data["equifax_account_date"] = $td->nodeValue;
                                        }
                                            

                                        

                                        $i++;
                                    }   


                                    $t++;
                                }
                            }
                        }

                        $add = false;
                        if($table_data['trans_union_account_status'] == "Derogatory" || stripos($table_data['trans_union_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['trans_union_payment_status'], "Late 30") !== false || stripos($table_data['trans_union_payment_status'], "Late 60") !== false || stripos($table_data['trans_union_payment_status'], "Late 90") !== false || stripos($table_data['trans_union_payment_status'], "Late 120") !== false || stripos($table_data['trans_union_payment_status'], "Late 150") !== false || stripos($table_data['trans_union_payment_status'], "Late 180") !== false) {
                            $derogatory_accounts["total"]++;
                            $table_data["unique_status"] = implode(", ", array_unique(array($table_data["trans_union_payment_status"], $table_data["experian_payment_status"], $table_data["equifax_payment_status"])));
                            $add = true;
                            //$derogatory_accounts['accounts'][] = $table_data;  
                        }

                        if($table_data['experian_account_status'] == "Derogatory" || stripos($table_data['experian_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['experian_payment_status'], "Late 30") !== false || stripos($table_data['experian_payment_status'], "Late 60") !== false || stripos($table_data['experian_payment_status'], "Late 90") !== false || stripos($table_data['experian_payment_status'], "Late 120") !== false || stripos($table_data['experian_payment_status'], "Late 150") !== false || stripos($table_data['experian_payment_status'], "Late 180") !== false) {
                            $derogatory_accounts["total"]++;
                            $table_data["unique_status"] = implode(", ", array_unique(array($table_data["trans_union_payment_status"], $table_data["experian_payment_status"], $table_data["equifax_payment_status"])));
                            $add = true;
                            //$derogatory_accounts['accounts'][] = $table_data;  
                        }

                        if($table_data['equifax_account_status'] == "Derogatory" || stripos($table_data['equifax_payment_status'], "Collection/Chargeoff" ) !== false || stripos($table_data['equifax_payment_status'], "Late 30") !== false || stripos($table_data['equifax_payment_status'], "Late 60") !== false || stripos($table_data['equifax_payment_status'], "Late 90") !== false || stripos($table_data['equifax_payment_status'], "Late 120") !== false || stripos($table_data['equifax_payment_status'], "Late 150") !== false || stripos($table_data['equifax_payment_status'], "Late 180") !== false) {
                            $derogatory_accounts["total"]++;
                            $table_data["unique_status"] = implode(", ", array_unique(array($table_data["trans_union_payment_status"], $table_data["experian_payment_status"], $table_data["equifax_payment_status"])));
                            $add = true;
                            //$derogatory_accounts['accounts'][] = $table_data;  
                        }

                        if($add === true) $derogatory_accounts['accounts'][] = $table_data; 

                        if(($table_data['trans_union_account_status'] == "Derogatory" || $table_data['experian_account_status'] == "Derogatory" || $table_data['equifax_account_status'] == "Derogatory" || stripos($table_data['trans_union_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['experian_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['equifax_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['trans_union_payment_status'], "Late 30") !== false || stripos($table_data['trans_union_payment_status'], "Late 60") !== false || stripos($table_data['trans_union_payment_status'], "Late 90") !== false || stripos($table_data['trans_union_payment_status'], "Late 120") !== false || stripos($table_data['trans_union_payment_status'], "Late 150") !== false || stripos($table_data['trans_union_payment_status'], "Late 180") !== false || stripos($table_data['experian_payment_status'], "Late 30") !== false || stripos($table_data['experian_payment_status'], "Late 60") !== false || stripos($table_data['experian_payment_status'], "Late 90") !== false || stripos($table_data['experian_payment_status'], "Late 120") !== false || stripos($table_data['experian_payment_status'], "Late 150") !== false || stripos($table_data['experian_payment_status'], "Late 180") !== false || stripos($table_data['equifax_payment_status'], "Late 30") !== false || stripos($table_data['equifax_payment_status'], "Late 60") !== false || stripos($table_data['equifax_payment_status'], "Late 90") !== false || stripos($table_data['equifax_payment_status'], "Late 120") !== false || stripos($table_data['equifax_payment_status'], "Late 150") !== false || stripos($table_data['equifax_payment_status'], "Late 180") !== false) && $add === false) {


                            if($table_data['trans_union_account_status'] == "Derogatory" || stripos($table_data['trans_union_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['trans_union_payment_status'], "Late 30") !== false || stripos($table_data['trans_union_payment_status'], "Late 60") !== false || stripos($table_data['trans_union_payment_status'], "Late 90") !== false || stripos($table_data['trans_union_payment_status'], "Late 120") !== false || stripos($table_data['trans_union_payment_status'], "Late 150") !== false || stripos($table_data['trans_union_payment_status'], "Late 180") !== false) {
                                //$derogatory_accounts["total"]++;
                                //error_log("JOE: TRANS ADD");
                            } else {
                                $table_data['trans_union_payment_status'] = "";
                                $table_data['trans_union_account_date'] = "";
                            }
    
                            if($table_data['experian_account_status'] == "Derogatory" || stripos($table_data['experian_payment_status'], "Collection/Chargeoff") !== false || stripos($table_data['experian_payment_status'], "Late 30") !== false || stripos($table_data['experian_payment_status'], "Late 60") !== false || stripos($table_data['experian_payment_status'], "Late 90") !== false || stripos($table_data['experian_payment_status'], "Late 120") !== false || stripos($table_data['experian_payment_status'], "Late 150") !== false || stripos($table_data['experian_payment_status'], "Late 180") !== false) {
                                //$derogatory_accounts["total"]++;
                                //error_log("JOE: EXP ADD");
                            } else {
                                $table_data['experian_payment_status'] = "";
                                $table_data['experian_account_date'] = "";
                            }
    
                            if($table_data['equifax_account_status'] == "Derogatory" || stripos($table_data['equifax_payment_status'], "Collection/Chargeoff" ) !== false || stripos($table_data['equifax_payment_status'], "Late 30") !== false || stripos($table_data['equifax_payment_status'], "Late 60") !== false || stripos($table_data['equifax_payment_status'], "Late 90") !== false || stripos($table_data['equifax_payment_status'], "Late 120") !== false || stripos($table_data['equifax_payment_status'], "Late 150") !== false || stripos($table_data['equifax_payment_status'], "Late 180") !== false) {
                                //$derogatory_accounts["total"]++;
                                //error_log("JOE: EQ ADD");
                            } else {
                                $table_data['equifax_payment_status'] = "";
                                $table_data['equifax_account_date'] = "";
                            }

                            $table_data["unique_status"] = implode(", ", array_unique(array($table_data["trans_union_payment_status"], $table_data["experian_payment_status"], $table_data["equifax_payment_status"])));
                            $derogatory_accounts['accounts'][] = $table_data;
                            //$derogatory_accounts['total']++;
                        } 
                    } 
                }
            }

            return $derogatory_accounts;

        }

        private function get_personal_info($doc) {

            $tables = $doc->getElementsByTagName("table");
            $response = "";
            $x = 0;
            $values = array();

            foreach($tables as $table) {
                $class = $table->getAttribute('class');

                if(stripos($class,"rpt_content_table") !== false) {
                    $tds = $table->getElementsByTagName("td");
                    foreach($tds as $td) {
                        $response .= "{$x} - {$td->nodeValue}</br>";
                        $values[] = $td->nodeValue;



                        $x++;
                    }
                }

            }

            $this->client_data = new \stdClass;

            // Trans Union
            $this->client_data->trans_union = array();
            $this->client_data->trans_union['report_data'] = $values[7];
            $this->client_data->trans_union['name'] = $values[11];
            $this->client_data->trans_union['also_known_as'] = $values[15];
            $this->client_data->trans_union['former_name'] = $values[19];
            $this->client_data->trans_union['date_of_birth'] = $values[23];
            $this->client_data->trans_union['current_address'] = $values[27];
            $this->client_data->trans_union['previous_address'] = $values[31];
            $this->client_data->trans_union['employers'] = $values[35];
            $this->client_data->trans_union['credit_score'] = $values[39];
            $this->client_data->trans_union['lending_rank'] = $values[43];
            $this->client_data->trans_union['score_scale'] = $values[47];
            $this->client_data->trans_union['total_accounts'] = $values[58];
            $this->client_data->trans_union['open_accounts'] = $values[62];
            $this->client_data->trans_union['closed_accounts'] = $values[66];
            $this->client_data->trans_union['delinquent'] = $values[70];
            $this->client_data->trans_union['derogatory'] = $values[74];
            $this->client_data->trans_union['collection'] = $values[78];
            $this->client_data->trans_union['balances'] = $values[82];
            $this->client_data->trans_union['payments'] = $values[86];
            $this->client_data->trans_union['public_records'] = $values[90];
            $this->client_data->trans_union['inquiries'] = $values[94];

            // Experian
            $this->client_data->experian = array();
            $this->client_data->experian['report_data'] = $values[8];
            $this->client_data->experian['name'] = $values[12];
            $this->client_data->experian['also_known_as'] = $values[16];
            $this->client_data->experian['former_name'] = $values[20];
            $this->client_data->experian['date_of_birth'] = $values[24];
            $this->client_data->experian['current_address'] = $values[28];
            $this->client_data->experian['previous_address'] = $values[32];
            $this->client_data->experian['employers'] = $values[36];
            $this->client_data->experian['credit_score'] = $values[40];
            $this->client_data->experian['lending_rank'] = $values[45];
            $this->client_data->experian['score_scale'] = $values[48];
            $this->client_data->experian['total_accounts'] = $values[59];
            $this->client_data->experian['open_accounts'] = $values[63];
            $this->client_data->experian['closed_accounts'] = $values[67];
            $this->client_data->experian['delinquent'] = $values[71];
            $this->client_data->experian['derogatory'] = $values[75];
            $this->client_data->experian['collection'] = $values[79];
            $this->client_data->experian['balances'] = $values[83];
            $this->client_data->experian['payments'] = $values[87];
            $this->client_data->experian['public_records'] = $values[91];
            $this->client_data->experian['inquiries'] = $values[95];

            // Equifax
            $this->client_data->equifax = array();
            $this->client_data->equifax['report_data'] = $values[9];
            $this->client_data->equifax['name'] = $values[13];
            $this->client_data->equifax['also_known_as'] = $values[16];
            $this->client_data->equifax['former_name'] = $values[21];
            $this->client_data->equifax['date_of_birth'] = $values[25];
            $this->client_data->equifax['current_address'] = $values[29];
            $this->client_data->equifax['previous_address'] = $values[33];
            $this->client_data->equifax['employers'] = $values[37];
            $this->client_data->equifax['credit_score'] = $values[41];
            $this->client_data->equifax['lending_rank'] = $values[45];
            $this->client_data->equifax['score_scale'] = $values[49];
            $this->client_data->equifax['total_accounts'] = $values[60];
            $this->client_data->equifax['open_accounts'] = $values[64];
            $this->client_data->equifax['closed_accounts'] = $values[68];
            $this->client_data->equifax['delinquent'] = $values[72];
            $this->client_data->equifax['derogatory'] = $values[76];
            $this->client_data->equifax['collection'] = $values[80];
            $this->client_data->equifax['balances'] = $values[84];
            $this->client_data->equifax['payments'] = $values[88];
            $this->client_data->equifax['public_records'] = $values[92];
            $this->client_data->equifax['inquiries'] = $values[96];
            

            
            if(isset($values[165]) && stripos($values[165], "Account #:") !== false) {

                $set = true;
                $i = 165;


                
                while($set == true) {

                    if(isset($values[$i]) && stripos($values[$i], "Account #:") !== false) {
                        $test_trans_union = strtolower(str_replace("+","",urlencode($values[$i + 17])));
                        $test_experian = strtolower(str_replace("+","",urlencode($values[$i + 18])));
                        $test_equifax = strtolower(str_replace("+","",urlencode($values[$i + 19])));
                        
                        $i += 193;
                    } else {
                        $set = false;
                    }
                }
                
            } 
            
            
            

            return $values;
        }

    }

?>
