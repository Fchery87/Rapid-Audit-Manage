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

        public function Accounts(ManagerRegistry $doctrine) {

            return $this->render("report/accounts.html.twig", [
                "accounts"    =>  $this->get_accounts($doctrine),
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

        public function edit_Accounts(Request $request, ManagerRegistry $doctrine) {

            $aid = $request->query->get('aid');

            $messages = array();
            $errors = array();

            $parameters = array(
                "first_name"                =>  $request->request->get('first_name', ""),
                "last_name"                 =>  $request->request->get('last_name', ""),
                "email"                     =>  $request->request->get('email', ""),
                "phone"                     =>  $request->request->get('phone', ""),
                "address1"                  =>  $request->request->get('address1', ""),
                "address2"                  =>  $request->request->get('address2', ""),
                "city"                      =>  $request->request->get('city', ""),
                "state"                     =>  $request->request->get('state', ""),
                "zip"                       =>  $request->request->get('zip', ""),
                "social"                    =>  $request->request->get('social', ""),
                "credit_company"            =>  $request->request->get('credit_company', ""),
                "credit_company_user"       =>  $request->request->get('credit_company_user', ""),
                "credit_company_password"   =>  $request->request->get('credit_company_password', ""),
                "credit_company_code"       =>  $request->request->get('credit_company_code', ""),
            );

            if ($request->isMethod('POST')) {

                if (!$this->isCsrfTokenValid('account_edit_form', $request->request->get('_token'))) {
                    $errors[] = "Invalid CSRF token.";
                }

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
                    $em = $doctrine->getManager();
                    $conn = $em->getConnection();
                    $query = "UPDATE accounts SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, address1 = :address1, address2 = :address2, city = :city, state = :state, zip = :zip, social = :social, credit_company = :credit_company, credit_company_user = :credit_company_user, credit_company_password = :credit_company_password, credit_company_code = :credit_company_code WHERE aid = :aid LIMIT 1";
                    $statement = $conn->prepare($query);
                    $affected = $statement->executeStatement([
                        "first_name" => $parameters['first_name'],
                        "last_name" => $parameters['last_name'],
                        "email" => $parameters['email'],
                        "phone" => $parameters['phone'],
                        "address1" => $parameters['address1'],
                        "address2" => $parameters['address2'],
                        "city" => $parameters['city'],
                        "state" => $parameters['state'],
                        "zip" => $parameters['zip'],
                        "social" => $parameters['social'],
                        "credit_company" => $parameters['credit_company'],
                        "credit_company_user" => $parameters['credit_company_user'],
                        "credit_company_password" => $parameters['credit_company_password'],
                        "credit_company_code" => $parameters['credit_company_code'],
                        "aid" => $aid,
                    ]);
                    if($affected >= 0) {
                        $messages[] = "Account sucessfully udpated!";
                    } else {
                        $errors[] = "Failed to update record. Contact admin";
                    }
                }
            }

            if($aid) {
                $account_info = $this->get_accounts($doctrine, $aid);

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

        public function add_Accounts(Request $request, ManagerRegistry $doctrine) {

            $messages = array();
            $errors = array();

            $parameters = array(
                "first_name"                =>  $request->request->get('first_name', ""),
                "last_name"                 =>  $request->request->get('last_name', ""),
                "email"                     =>  $request->request->get('email', ""),
                "phone"                     =>  $request->request->get('phone', ""),
                "address1"                  =>  $request->request->get('address1', ""),
                "address2"                  =>  $request->request->get('address2', ""),
                "city"                      =>  $request->request->get('city', ""),
                "state"                     =>  $request->request->get('state', ""),
                "zip"                       =>  $request->request->get('zip', ""),
                "social"                    =>  $request->request->get('social', ""),
                "credit_company"            =>  $request->request->get('credit_company', ""),
                "credit_company_user"       =>  $request->request->get('credit_company_user', ""),
                "credit_company_password"   =>  $request->request->get('credit_company_password', ""),
                "credit_company_code"       =>  $request->request->get('credit_company_code', ""),
            );

            if ($request->isMethod('POST')) {

                if (!$this->isCsrfTokenValid('account_add_form', $request->request->get('_token'))) {
                    $errors[] = "Invalid CSRF token.";
                }

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
                    $em = $doctrine->getManager();
                    $conn = $em->getConnection();
                    // Check to see if email already exists
                    $query = "SELECT COUNT(*) `rows` FROM accounts WHERE email = :email";
                    $rows = $conn->prepare($query)->executeQuery(["email" => $parameters['email']])->fetchAllAssociative();

                    if($rows[0]['rows'] > 0) {
                        $errors[] = "Email address already exists";
                    } else {
                        $query = "INSERT INTO accounts SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, address1 = :address1, address2 = :address2, city = :city, state = :state, zip = :zip, social = :social, credit_company = :credit_company, credit_company_user = :credit_company_user, credit_company_password = :credit_company_password, credit_company_code = :credit_company_code, created = NOW()";
                        $affected = $conn->prepare($query)->executeStatement([
                            "first_name" => $parameters['first_name'],
                            "last_name" => $parameters['last_name'],
                            "email" => $parameters['email'],
                            "phone" => $parameters['phone'],
                            "address1" => $parameters['address1'],
                            "address2" => $parameters['address2'],
                            "city" => $parameters['city'],
                            "state" => $parameters['state'],
                            "zip" => $parameters['zip'],
                            "social" => $parameters['social'],
                            "credit_company" => $parameters['credit_company'],
                            "credit_company_user" => $parameters['credit_company_user'],
                            "credit_company_password" => $parameters['credit_company_password'],
                            "credit_company_code" => $parameters['credit_company_code'],
                        ]);
                        if($affected > 0) {
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



        private function get_accounts(ManagerRegistry $doctrine, $aid = null) {

            if(is_null($aid)) {
                // Include latest uploaded file (if any) for quick access to Simple Audit
                $query = "
                    SELECT a.*, f.filename AS latest_file, f.added AS latest_added
                    FROM accounts a
                    LEFT JOIN (
                        SELECT t1.aid, t1.filename, t1.added
                        FROM account_files t1
                        INNER JOIN (
                            SELECT aid, MAX(added) AS max_added
                            FROM account_files
                            GROUP BY aid
                        ) t2 ON t1.aid = t2.aid AND t1.added = t2.max_added
                    ) f ON a.aid = f.aid
                    ORDER BY a.last_name DESC
                ";
                $params = [];
            } else {
                $query = "SELECT * FROM accounts WHERE aid = :aid LIMIT 1";
                $params = ["aid" => $aid];
            }

            $em = $doctrine->getManager();
            $conn = $em->getConnection();

            return $conn->prepare($query)->executeQuery($params)->fetchAllAssociative();

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
