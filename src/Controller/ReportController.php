<?php

    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Doctrine\Persistence\ManagerRegistry;
    use App\Service\FileStorage;
    use App\Form\AccountType;

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

            if(!$aid) {
                return $this->render("report/accounts-view-not-found.html.twig", []);
            }

            $account_info = $this->get_accounts($doctrine, $aid);
            if(count($account_info) == 0) {
                return $this->render("report/accounts-view-not-found.html.twig", []);
            }
            $initial = $account_info[0];

            // Build Symfony Form with initial data
            $form = $this->createForm(AccountType::class, [
                "first_name" => $initial['first_name'] ?? "",
                "last_name" => $initial['last_name'] ?? "",
                "email" => $initial['email'] ?? "",
                "phone" => $initial['phone'] ?? "",
                "address1" => $initial['address1'] ?? "",
                "address2" => $initial['address2'] ?? "",
                "city" => $initial['city'] ?? "",
                "state" => $initial['state'] ?? "",
                "zip" => $initial['zip'] ?? "",
                "social" => $initial['social'] ?? "",
                "credit_company" => $initial['credit_company'] ?? "",
                "credit_company_user" => $initial['credit_company_user'] ?? "",
                "credit_company_password" => $initial['credit_company_password'] ?? "",
                "credit_company_code" => $initial['credit_company_code'] ?? "",
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $em = $doctrine->getManager();
                $conn = $em->getConnection();

                // Ensure email is unique (excluding current account)
                $check = $conn->prepare("SELECT COUNT(*) `rows` FROM accounts WHERE email = :email AND aid != :aid")
                    ->executeQuery(["email" => $data['email'], "aid" => $aid])
                    ->fetchAllAssociative();
                if (!empty($check) && (int)$check[0]['rows'] > 0) {
                    $errors[] = "Email address already exists";
                } else {
                    $query = "UPDATE accounts SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, address1 = :address1, address2 = :address2, city = :city, state = :state, zip = :zip, social = :social, credit_company = :credit_company, credit_company_user = :credit_company_user, credit_company_password = :credit_company_password, credit_company_code = :credit_company_code WHERE aid = :aid LIMIT 1";
                    $statement = $conn->prepare($query);
                    $affected = $statement->executeStatement([
                        "first_name" => $data['first_name'],
                        "last_name" => $data['last_name'],
                        "email" => $data['email'],
                        "phone" => $data['phone'],
                        "address1" => $data['address1'],
                        "address2" => $data['address2'],
                        "city" => $data['city'],
                        "state" => $data['state'],
                        "zip" => $data['zip'],
                        "social" => $data['social'],
                        "credit_company" => $data['credit_company'],
                        "credit_company_user" => $data['credit_company_user'],
                        "credit_company_password" => $data['credit_company_password'],
                        "credit_company_code" => $data['credit_company_code'],
                        "aid" => $aid,
                    ]);
                    if($affected >= 0) {
                        $messages[] = "Account sucessfully udpated!";
                    } else {
                        $errors[] = "Failed to update record. Contact admin";
                    }
                }
            }

            return $this->render("report/accounts-edit.html.twig", [
                "errors"        =>  $errors,
                "messages"      =>  $messages,
                "parameters"    =>  $initial,
                "form"          =>  $form->createView(),
            ]);
        }

        public function add_Accounts(Request $request, ManagerRegistry $doctrine) {

            $messages = array();
            $errors = array();

            $form = $this->createForm(AccountType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $em = $doctrine->getManager();
                $conn = $em->getConnection();
                // Check to see if email already exists
                $query = "SELECT COUNT(*) `rows` FROM accounts WHERE email = :email";
                $rows = $conn->prepare($query)->executeQuery(["email" => $data['email']])->fetchAllAssociative();

                if($rows[0]['rows'] > 0) {
                    $errors[] = "Email address already exists";
                } else {
                    $query = "INSERT INTO accounts SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, address1 = :address1, address2 = :address2, city = :city, state = :state, zip = :zip, social = :social, credit_company = :credit_company, credit_company_user = :credit_company_user, credit_company_password = :credit_company_password, credit_company_code = :credit_company_code, created = NOW()";
                    $affected = $conn->prepare($query)->executeStatement([
                        "first_name" => $data['first_name'],
                        "last_name" => $data['last_name'],
                        "email" => $data['email'],
                        "phone" => $data['phone'],
                        "address1" => $data['address1'],
                        "address2" => $data['address2'],
                        "city" => $data['city'],
                        "state" => $data['state'],
                        "zip" => $data['zip'],
                        "social" => $data['social'],
                        "credit_company" => $data['credit_company'],
                        "credit_company_user" => $data['credit_company_user'],
                        "credit_company_password" => $data['credit_company_password'],
                        "credit_company_code" => $data['credit_company_code'],
                    ]);
                    if($affected > 0) {
                        $messages[] = "Account sucessfully added!";
                        // Optionally redirect after success
                        // return $this->redirect('/accounts');
                    } else {
                        $errors[] = "Failed to create account.";
                    }
                }
            }

            return $this->render("report/accounts-add.html.twig", [
                "errors"        =>  $errors,
                "messages"      =>  $messages,
                "form"          =>  $form->createView(),
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
