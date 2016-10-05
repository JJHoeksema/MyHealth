<?php
    namespace Main\Controller;

    use Auth\Account;
    use DMF\Events\StatusChangeEvent;
    use Main\NoPermission;
    use DMF\Page;
    use DMF\Data;
    use DMF\Db_config;


    class indexController extends NoPermission{

        private $userModel;
        protected $db;
        private $email;
        public function test() {
            if(!$this->user->isLoggedIn()) return;
            $array = [];
            $this->app->getPage()->clear();
            $mail = new Page\Template("Mail/main");
            $welkom = new Page\Template("Mail/welcome");
            $welkom->add("name", new Page\Text($this->user->getFirstName()));
            $welkom->add("key", new Page\Text($this->user->getFirstName()));
            $mail->add("content", $welkom);
            $mail->add("Title", new Page\Text("Welkom bij MyHealth"));
            echo $mail->render();
        }

        public function activate(){
            if($this->user->verify($this->input->arg(0), $this->input->arg(1))){
                $this->template->add("content", new Page\Template('mainAttr/register/verified'));
            }else {
                $this->request->redirect();
            }
        }

        public function index(){
            $this->setTitle("Home | MyHealth");
            $welcome = new Page\Template("mainAttr/welcome");
            $this->template->add("content", $welcome);
        }

        private function validateNaw(){
            if($this->input->post("email") == NULL
                || !filter_var($this->input->post("email"), FILTER_VALIDATE_EMAIL)) return false;

            //TODO: regex
            if($this->input->post("naam")       == NULL) return false;
            if($this->input->post("achternaam") == NULL) return false;  //regex
            if($this->input->post("telnummer1") == NULL) return false;  //regex
            //if($this->input->post("telnummer2") == NULL) return false;  //regex
            if($this->input->post("reknummer")  == NULL) return false;   //regex
            if($this->input->post("land")       == NULL) return false;
            if($this->input->post("plaats")     == NULL) return false;
            if($this->input->post("postcode")   == NULL) return false;    //regex
            if($this->input->post("straat")     == NULL) return false;
            if($this->input->post("huisnummer") == NULL
                || !filter_var($this->input->post("huisnummer", FILTER_VALIDATE_INT))) return false;
            //if($this->input->post("toevoeging") == NULL) return false;
            return true;
        }

        private function createUser($id, Page\Template $template){
            $account = new Account();
            if($this->input->post("email") != null && !$account->userExist($this->input->post("email"))) {
                $result = $account->register($this->input->post("email"), $this->input->post("pass"), $id);
                if ($result != null) {
                    $this->userModel = new Data\FileModel("User");
                    $selector = new Data\Specifier\Where($this->userModel, [
                        new Data\Specifier\WhereCheck("email", "==", $this->input->post("email")),
                    ]);
                    $data = $this->db->select($this->userModel, null, $selector);
                    $this->mailUser($data[0]);

                    $this->request->redirect('main/index/register/success');
                    return true;
                }else{
                    $template->add("errors", new Page\Text("Er is iets misgegaan met het registreren<br/>", false));
                }
            }else {
                $template->add("errors", new Page\Text("Er is al een gebruiker met dit email adres<br/>", false));
            }
            return false;
        }

        private function mailUser($data) {
            $pw = $data["User-verify"];
            $email = $data["User-email"];
            $emailFrom = "support@myhealth.nl";

            $text = "Beste Klant,<br><br>
                     Er is een account voor u aangemaakt in onze MyHealth service. Om dit account te activeren kunt u met uw emailadres en het tijdelijke wachtwoord $pw inloggen op de volgende link:<br>
                     <a href=\"http://myhealth.niekgigengack.nl/main/index/login\">MyHealth Inloggen</a><br><br>
                     Met vriendelijke groet,<br><br>
                     De MyHealth klantenservice.";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html; charset=utf-8" . "\r\n";
            $headers .= "From: <$emailFrom>" . "\r\n";
            mail($email, "MyHealth Account Registratie", $text, $headers);
        }

        private function getRegisterTemplate(){
            $register = new Page\Template("mainAttr/register");

            $register->add("email",         new Page\Text($this->input->post("email"))      );
            $register->add("naam",          new Page\Text($this->input->post("naam"))       );
            $register->add("achternaam",    new Page\Text($this->input->post("achternaam")) );
            $register->add("telnummer1",    new Page\Text($this->input->post("telnummer1")) );
            $register->add("telnummer2",    new Page\Text($this->input->post("telnummer2")) );
            $register->add("reknummer",     new Page\Text($this->input->post("reknummer"))  );
            $register->add("land",          new Page\Text($this->input->post("land"))       );
            $register->add("plaats",        new Page\Text($this->input->post("plaats"))     );
            $register->add("postcode",      new Page\Text($this->input->post("postcode"))   );
            $register->add("straat",        new Page\Text($this->input->post("straat"))     );
            $register->add("huisnummer",    new Page\Text($this->input->post("huisnummer")) );
            $register->add("toevoeging",    new Page\Text($this->input->post("toevoeging")) );

            return $register;
        }

        public function register() {
            $nawModel = new Data\FileModel("Naw");
            $this->setTitle("Aanmelden | MyHealth");
            $config = new Db_config();
            $this->db           =   new Data\MySQLDatabase($config->getHost(), $config->getUsername(), $config->getPw());

            if($this->input->arg(0) == "success"){
                // hier was eerst usermodel.. kan weg als werkt
                $this->template->add("content", new Page\Template("mainAttr/register-success"));
                return;
            }

            if(!$this->validateNaw()){
                $register = $this->getRegisterTemplate();
                $this->template->add("content",$register);
                return;
            }

            if((new Account)->userExist($this->input->post("email"))){
                $register = $this->getRegisterTemplate();
                $this->template->add("content",$register);
                $register->add("errors", new Page\Text("Er is al een gebruiker met dit email adres"));
                return;
            };


            $data = [
                "naam"          => $this->input->post("naam"),
                "achternaam"    => $this->input->post("achternaam"),
                "telnummer1"    => $this->input->post("telnummer1"),
                "telnummer2"    => $this->input->post("telnummer2"),
                "reknummer"     => $this->input->post("reknummer"),
                "land"          => $this->input->post("land"),
                "plaats"        => $this->input->post("plaats"),
                "postcode"      => $this->input->post("postcode"),
                "straat"        => $this->input->post("straat"),
                "huisnummer"    => $this->input->post("huisnummer"),
                "toevoeging"    => $this->input->post("toevoeging"),
            ];

            $result = $this->db->insert($nawModel, $data);
            $id = $result['Naw-id'];
            $template = $this->getRegisterTemplate();

            if($this->createUser($id, $template)){
                $this->request->redirect('main/index/register/success');
                return;
            }else{
                $this->template->add("content", $template);
            }
        }

        public function login() {
            if($this->user->isLoggedIn()){
                $this->request->redirect();
                return;
            }
            $this->app->activateEvent(new StatusChangeEvent(401));
        }

        public function logout(){
            $this->user->logOut();
            $this->request->redirect();
        }

        public function change(){
            $this->setTitle("Login | MyHealth");
            $change = new Page\Template("mainAttr/register/changepw");
            if($_POST["pass"]!=null){
                if($_POST['pass']==$_POST["pass2"]){
                    $config = new Db_config();
                    $this->db           =   new Data\MySQLDatabase($config->getHost(), $config->getUsername(), $config->getPw());
                    $this->userModel    =   new Data\FileModel("User");
                    $selector = new Data\Specifier\Where($this->userModel, [
                        new Data\Specifier\WhereCheck("verify", "==", $_SESSION['ver']),
                    ]);
                    $pass = password_hash($_POST['pass'], CRYPT_BLOWFISH,
                        ['cost' => 12]);
                    $this->db->update($this->userModel, ["verify" => null, "verified" => 1,"password" => $pass], $selector);
                    $change->add("msg", new Page\Text("Het wachtwoord is gewijzigd."));
                }
                else{
                    $change->add("msg", new Page\Text("De wachtwoorden komen niet overeen"));
                }
            }
            $this->template->add("content", $change);
        }


    }