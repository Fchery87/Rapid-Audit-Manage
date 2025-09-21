<?php

    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Doctrine\Persistence\ManagerRegistry;
    use App\Service\FileStorage;

    class ReportController extends AbstractController {

        private $errors = array();
        private $message = "";
        private $directory = "var/uploads";

        public function dashboard() {
            return $this->render("dashboard/dashboard-sample.html.twig", [
            ]);
        }

        public function __init(Request $request, FileStorage $storage) {

            $filename = "none";
            $errors = [];

            if ($request->isMethod('POST') && $request->request->get('upload') === "true") {

                if (!$this->isCsrfTokenValid('global_upload', $request->request->get('_token'))) {
                    $errors[] = 'Invalid CSRF token.';
                }

                $uploadedFile = $request->files->get('html_file');
                if (!$uploadedFile) {
                    $errors[] = 'No file sent';
                }

                if (count($errors) === 0) {
                    try {
                        $stored = $storage->storeUploadedHtml($uploadedFile);
                        $filename = $stored;
                        $this->message = "Upload successful!";
                    } catch (\RuntimeException $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            }

            return $this->render("report/report.html.twig", [
                "filename"      =>      $filename,
                "errors"        =>      $errors,
                "message"       =>      $this->message,
                "files"         =>      $storage->listHtmlFilenames(),
            ]);
        }

        public function Accounts() {

            return $this->render("report/accounts.html.twig", [
                "accounts"    =>  $this->get_accounts(),
            ]);
        }

        public function view_Accounts(Request $request, ManagerRegistry $doctrine, FileStorage $storage) {

            $aid = $request->query->get('aid');
            $errors = array();
            $messages = array();

            if($aid) {
                $em = $doctrine->getManager();
                $conn = $em->getConnection();
                $query = "SELECT * FROM accounts WHERE aid = :aid LIMIT 1";
                $statement = $conn->prepare($query);
                $result = $statement->executeQuery(['aid' => $aid]);
                $rows = $result->fetchAllAssociative();

                if(count($rows) > 0) {

                    // Upload
                    if ($request->isMethod('POST') && $request->request->get('upload')) {

                        if (!$this->isCsrfTokenValid('account_upload', $request->request->get('_token'))) {
                            $errors[] = "Invalid CSRF token.";
                        }

                        $uploadedFile = $request->files->get('html_file');
                        if (!$uploadedFile) {
                            $errors[] = "No file sent";
                        }

                        if(count($errors) === 0) {
                            try {
                                $storedName = $storage->storeUploadedHtml($uploadedFile);

                                // Prevent duplicate association for this account+file
                                $query = "SELECT * FROM account_files WHERE aid = :aid AND filename = :filename";
                                $statement = $conn->prepare($query);
                                $result = $statement->executeQuery(['aid' => $aid, 'filename' => $storedName]);
                                $file_check = $result->fetchAllAssociative();

                                if(count($file_check) == 0) {
                                    $query = "INSERT INTO account_files SET aid = :aid, filename = :filename, added = NOW()";
                                    $statement = $conn->prepare($query);
                                    $statement->executeStatement(['aid' => $aid, 'filename' => $storedName]);

                                    $messages[] = "Upload successful!";
                                } else {
                                    $errors[] = "File already exists for this user.";
                                }

                            } catch (\RuntimeException $e) {
                                $errors[] = $e->getMessage();
                            }
                        }
                    }

                    // Get all reports
                    $query = "SELECT * FROM account_files WHERE aid = :aid ORDER BY added DESC";
                    $statement = $conn->prepare($query);
                    $result = $statement->executeQuery(['aid' => $aid]);
                    $files = $result->fetchAllAssociative();

                    return $this->render("report/accounts-view.html.twig", [
                        "account_info"  =>  $rows[0],
                        "errors"        =>  $errors,
                        "messages"      =>  $messages,
                        "files"         =>  $files,
                    ]);
                } else {
                    return $this->render("report/accounts-view-not-found.html.twig", []);
                }

            } else {
                return $this->render("report/accounts-view-not-found.html.twig", []);
            }
        }

        public function edit_Accounts() {

            $aid = isset($_GET['aid']) ? filter_var($_GET['aid'], FILTER_SANITIZE_STRING) : null;


            
            $messages = array();
            $errors = array();

            $parameters = array(
                "first_name"                =>  isset($_POST['first_name']) ? filter_var($_POST['first_name'], FILTER_SANITIZE_STRING) : "",
                "last_name"                 =>  isset($_POST['last_name']) ? filter_var($_POST['last_name'], FILTER_SANITIZE_STRING) : "",
                "email"                     =>  isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_STRING) : "",
                "phone"                     =>  isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : "",
                "address1"                  =>  isset($_POST['address1']) ? filter_var($_POST['address1'], FILTER_SANITIZE_STRING) : "",
                "address2"                  =>  isset($_POST['address2']) ? filter_var($_POST['address2'], FILTER_SANITIZE_STRING) : "",
                "city"                      =>  isset($_POST['city']) ? filter_var($_POST['city'], FILTER_SANITIZE_STRING) : "",
                "state"                     =>  isset($_POST['state']) ? filter_var($_POST['state'], FILTER_SANITIZE_STRING) : "",
                "zip"                       =>  isset($_POST['zip']) ? filter_var($_POST['zip'], FILTER_SANITIZE_STRING) : "",
                "social"                    =>  isset($_POST['social']) ? filter_var($_POST['social'], FILTER_SANITIZE_STRING) : "",
                "credit_company"            =>  isset($_POST['credit_company']) ? filter_var($_POST['credit_company'], FILTER_SANITIZE_STRING) : "",
                "credit_company_user"       =>  isset($_POST['credit_company_user']) ? filter_var($_POST['credit_company_user'], FILTER_SANITIZE_STRING) : "",
                "credit_company_password"   =>  isset($_POST['credit_company_password']) ? filter_var($_POST['credit_company_password'], FILTER_SANITIZE_STRING) : "",
                "credit_company_code"       =>  isset($_POST['credit_company_code']) ? filter_var($_POST['credit_company_code'], FILTER_SANITIZE_STRING) : "",
            );

            if(isset($_POST['first_name']) && isset($_POST['first_name']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['address1']) && isset($_POST['address2']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['zip']) && isset($_POST['social']) && isset($_POST['credit_company']) && isset($_POST['credit_company_user']) && isset($_POST['credit_company_password']) && isset($_POST['credit_company_code'])) {
                

                if($this->validate_name($parameters['first_name']) == false) {
                    $errors[] = "Invalid First Name.";
                }
                if($this->validate_name($parameters['last_name']) == false) {
                    $errors[] = "Invalid Last Name";
                }
                if($this->validate_name($parameters['email']) == false) {
                    $errors[] = "Invalid Email";
                }
                if($this->validate_name($parameters['phone']) == false) {
                    $errors[] = "Invalid Phone Number";
                }
                if($this->validate_name($parameters['address1']) == false) {
                    $errors[] = "Invalid Address1";
                }
                if($this->validate_name($parameters['city']) == false) {
                    $errors[] = "Invalid City";
                }
                if($this->validate_name($parameters['zip']) == false) {
                    $errors[] = "Invalid Zip Code";
                }
                if($this->validate_name($parameters['social']) == false) {
                    $errors[] = "Invalid Social";
                }

                if(count($errors) == 0) {
                    // Get Connection
                    $em = $this->getDoctrine()->getManager();
                    // Validate all otehr values
                    $query = "UPDATE accounts SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, address1 = :address1, address2 = :address2, city = :city, state = :state, zip = :zip, social = :social, credit_company = :credit_company, credit_company_user = :credit_company_user, credit_company_password = :credit_company_password, credit_company_code = :credit_company_code WHERE aid = :aid LIMIT 1";
                    $statement = $em->getConnection()->prepare($query);
                    $statement->bindValue("first_name", $parameters['first_name']);
                    $statement->bindValue("last_name", $parameters['last_name']);
                    $statement->bindValue("email", $parameters['email']);
                    $statement->bindValue("phone", $parameters['phone']);
                    $statement->bindValue("address1", $parameters['address1']);
                    $statement->bindValue("address2", $parameters['address2']);
                    $statement->bindValue("city", $parameters['city']);
                    $statement->bindValue("state", $parameters['state']);
                    $statement->bindValue("zip", $parameters['zip']);
                    $statement->bindValue("social", $parameters['social']);
                    $statement->bindValue("credit_company", $parameters['credit_company']);
                    $statement->bindValue("credit_company_user", $parameters['credit_company_user']);
                    $statement->bindValue("credit_company_password", $parameters['credit_company_password']);
                    $statement->bindValue("credit_company_code", $parameters['credit_company_code']);
                    $statement->bindValue("aid", $aid);
                    $affected = $statement->executeStatement();
                    if($affected >= 0) {
                        $messages[] = "Account sucessfully udpated!";
                    } else {
                        $errors[] = "Failed to update record. Contact admin";
                    }
                }
            }

            if($aid) {
                $account_info = $this->get_accounts($aid);

                if(count($account_info) == 0) {
                    return $this->render("report/accounts-view-not-found.html.twig", []);
                }
            } else {
                return $this->render("report/accounts-view-not-found.html.twig", []);
            }

            return $this->render("report/accounts-edit.html.twig", [
                "errors"        =>  $errors,
                "messages"      =>  $messages,
                "parameters"    =>  $account_info[0]
            ]);
        }

        public function add_Accounts() {

            $messages = array();
            $errors = array();

            $parameters = array(
                "first_name"                =>  isset($_POST['first_name']) ? filter_var($_POST['first_name'], FILTER_SANITIZE_STRING) : "",
                "last_name"                 =>  isset($_POST['last_name']) ? filter_var($_POST['last_name'], FILTER_SANITIZE_STRING) : "",
                "email"                     =>  isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_STRING) : "",
                "phone"                     =>  isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : "",
                "address1"                  =>  isset($_POST['address1']) ? filter_var($_POST['address1'], FILTER_SANITIZE_STRING) : "",
                "address2"                  =>  isset($_POST['address2']) ? filter_var($_POST['address2'], FILTER_SANITIZE_STRING) : "",
                "city"                      =>  isset($_POST['city']) ? filter_var($_POST['city'], FILTER_SANITIZE_STRING) : "",
                "state"                     =>  isset($_POST['state']) ? filter_var($_POST['state'], FILTER_SANITIZE_STRING) : "",
                "zip"                       =>  isset($_POST['zip']) ? filter_var($_POST['zip'], FILTER_SANITIZE_STRING) : "",
                "social"                    =>  isset($_POST['social']) ? filter_var($_POST['social'], FILTER_SANITIZE_STRING) : "",
                "credit_company"            =>  isset($_POST['credit_company']) ? filter_var($_POST['credit_company'], FILTER_SANITIZE_STRING) : "",
                "credit_company_user"       =>  isset($_POST['credit_company_user']) ? filter_var($_POST['credit_company_user'], FILTER_SANITIZE_STRING) : "",
                "credit_company_password"   =>  isset($_POST['credit_company_password']) ? filter_var($_POST['credit_company_password'], FILTER_SANITIZE_STRING) : "",
                "credit_company_code"       =>  isset($_POST['credit_company_code']) ? filter_var($_POST['credit_company_code'], FILTER_SANITIZE_STRING) : "",
            );

            if(isset($_POST['first_name']) && isset($_POST['first_name']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['address1']) && isset($_POST['address2']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['zip']) && isset($_POST['social']) && isset($_POST['credit_company']) && isset($_POST['credit_company_user']) && isset($_POST['credit_company_password']) && isset($_POST['credit_company_code'])) {
                

                if($this->validate_name($parameters['first_name']) == false) {
                    $errors[] = "Invalid First Name.";
                }
                if($this->validate_name($parameters['last_name']) == false) {
                    $errors[] = "Invalid Last Name";
                }
                if($this->validate_name($parameters['email']) == false) {
                    $errors[] = "Invalid Email";
                }
                if($this->validate_name($parameters['phone']) == false) {
                    $errors[] = "Invalid Phone Number";
                }
                if($this->validate_name($parameters['address1']) == false) {
                    $errors[] = "Invalid Address1";
                }
                if($this->validate_name($parameters['city']) == false) {
                    $errors[] = "Invalid City";
                }
                if($this->validate_name($parameters['zip']) == false) {
                    $errors[] = "Invalid Zip Code";
                }
                if($this->validate_name($parameters['social']) == false) {
                    $errors[] = "Invalid Social";
                }

                if(count($errors) == 0) {
                    // Get Connection
                    $em = $this->getDoctrine()->getManager();
                    // Check to see if email already exists
                    $query = "SELECT COUNT(*) `rows` FROM accounts WHERE email = :email";
                    $statement = $em->getConnection()->prepare($query);
                    $statement->bindValue("email", $parameters['email']);
                    $result = $statement->executeQuery();
                    $rows = $result->fetchAllAssociative();

                    if($rows[0]['rows'] > 0) {
                        $errors[] = "Email address already exists";
                    } else {
                        // Validate all otehr values
                        $query = "INSERT INTO accounts SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, address1 = :address1, address2 = :address2, city = :city, state = :state, zip = :zip, social = :social, credit_company = :credit_company, credit_company_user = :credit_company_user, credit_company_password = :credit_company_password, credit_company_code = :credit_company_code, created = NOW()";
                        $statement = $em->getConnection()->prepare($query);
                        $statement->bindValue("first_name", $parameters['first_name']);
                        $statement->bindValue("last_name", $parameters['last_name']);
                        $statement->bindValue("email", $parameters['email']);
                        $statement->bindValue("phone", $parameters['phone']);
                        $statement->bindValue("address1", $parameters['address1']);
                        $statement->bindValue("address2", $parameters['address2']);
                        $statement->bindValue("city", $parameters['city']);
                        $statement->bindValue("state", $parameters['state']);
                        $statement->bindValue("zip", $parameters['zip']);
                        $statement->bindValue("social", $parameters['social']);
                        $statement->bindValue("credit_company", $parameters['credit_company']);
                        $statement->bindValue("credit_company_user", $parameters['credit_company_user']);
                        $statement->bindValue("credit_company_password", $parameters['credit_company_password']);
                        $statement->bindValue("credit_company_code", $parameters['credit_company_code']);
                        if($statement->executeStatement() > 0) {
                            $messages[] = "Account sucessfully added!";
                        }
                    }
                }
            }

            return $this->render("report/accounts-add.html.twig", [
                "errors"        =>  $errors,
                "messages"      =>  $messages,
                "parameters"    =>  $parameters
            ]);
        }



        private function get_accounts($aid = null) {

            if(is_null($aid)) {
                $query = "SELECT * FROM accounts ORDER BY last_name DESC";
            } else {
                $query = "SELECT * FROM accounts WHERE aid = :aid LIMIT 1";
            }

            $em = $this->getDoctrine()->getManager();
            $statement = $em->getConnection()->prepare($query);

            if(!is_null($aid)) {
                $statement->bindValue("aid", $aid);
            }

            return $statement->executeQuery()->fetchAllAssociative();

        }

        private function get_files() {

            $files = array();

            $listings = scandir($this->directory);

            foreach($listings as $listing) {
                if(($listing !== "." || $listing !== ".." || $listing != ".DS_Store") && !is_dir("{$this->directory}/{$listing}")) {
                    $mime = mime_content_type("{$this->directory}/{$listing}");
                    if($mime == "text/html") {
                        $files[] = $listing;
                    }
                }
            }

            return $files;
        }


        private function validate_email($email) {

            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            } else {
                return false;
            }
            
        }

        private function validate_phone($phone) {
            $numbersOnly = preg_replace("[^0-9]", "", $phone);
            $numberOfDigits = strlen($numbersOnly);
            if ($numberOfDigits == 7 or $numberOfDigits == 10) {
                return $numbersOnly;
            } else {
                return false;
            }
        }

        private function validate_address($address) {
            if(strlen($address) > 5) {
                return true;
            } else {
                return false;
            }
        }

        private function validate_city($city) {
            if(strlen($city) > 2) {
                return true;
            } else {
                return false;
            }
        } 

        private function validate_name($name) {
            if(strlen($name) > 2) {
                return true;
            } else {
                return false;
            }
        } 

        private function validate_zip($zip) {
            if(strlen($zip) == 5) {
                return true;
            } else {
                return false;
            }
        } 

        private function validate_social($social) {
            if(strlen($social) == 4) {
                return true;
            } else {
                return false;
            }
        } 

    }

?>
