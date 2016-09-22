<?php
    namespace Main\Controller;

    use Auth\Account;
    use DMF\Events\StatusChangeEvent;
    use Main\NoPermission;
    use DMF\Page;
    use DMF\Data;

    class indexController extends NoPermission{

        public function test() {
            if(!$this->user->isLoggedIn()) return;
            $array = [];
            $this->app->getPage()->clear();
            $mail = new Page\Template("Mail/main");
            $welkom = new Page\Template("Mail/welcome");
            $welkom->add("name", new Page\Text($this->user->getFirstName()));
            $welkom->add("key", new Page\Text($this->user->getFirstName()));
            $mail->add("content", $welkom);
            $mail->add("Title", new Page\Text("Welkom bij MyHealt"));
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
            $welcome->add("text", new Page\Text("
____________________
_______§§________§§§
_______§_§____§§___§
______§__§___§____§§
_____§__§§__§___§§
§§§__§__§__§___§§
§_§__§__§_§___§§__§§§§§
§__§_§__§§§___§§_§_____§§
§§_§_§___§§____§§__§§§___§
_§__§_§___§§___§§___§_§§§§§
__§__§§§___§§§§____§§____§_§
__§§__§§§§_____§____§____§__§
___§§_______________§§___§___§
_____§___§§___§§§____§§__§§§_§
______§___§§§___________§§___§
_______§_____§§__________§__§
_______§§_____§§____________§
________§______§§__________§§
________§_________________§§
________§__________§§____§§
________§§__________§___§§
_________§____________§§
_________§§_________§§
______§§§§__________§
_____§§§§________§_§
_____§__§__§§§§§§_§§
____§___§__§_____§§
____§§___§§______§"));
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

                    $mail = new Page\Template("Mail/main");
                    $welkom = new Page\Template("Mail/welcome");
                    $welkom->add("name", new Page\Text($this->input->post("naam")));
                    $welkom->add("key", new Page\Text($result['key']));
                    $welkom->add("date", new Page\Text($result['date']));
                    $mail->add("content", $welkom);
                    $mail->add("Title", new Page\Text("Welkom bij CityPark"));

                    $this->mailer->setBody($mail->render());
                    $this->mailer->subject("Welkom bij Citypark");
                    $this->mailer->send($this->input->post("email"));
                    $this->request->redirect('/main/index/register/success');
                    return true;
                }else{
                    $template->add("errors", new Page\Text("Er is iets misgegaan met het registreren<br/>", false));
                }
            }else {
                $template->add("errors", new Page\Text("Er is al een gebruiker met dit email adres<br/>", false));
            }
            return false;
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
            $this->setTitle("Aanmelden | CityPark");

            if($this->input->arg(0) == "success"){
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

    }