<?php
    namespace Api\Controller;

    use Api\NoPermission;
    use DMF\Data;
    use DMF\App;
    use DMF\Db_config;
    class indexController extends NoPermission{
        private $userModel;
        private $nawModel;


        public function measurements(){
            App::getInstance()->getPage()->clear();
            $token= $this->input->arg(0);


            if($this->verifyCode($token)) {
                $this->construct();
                $content = $this->loadContent();



                return $this->error();
            } else return $this->error();
        }

        public function login() {
            App::getInstance()->getPage()->clear();
            $token= $this->input->arg(0);


            if($this->verifyCode($token)) {
                $this->construct();
                $content = $this->loadContent();

                $username = $content->Useremail;
                $password = $content->Userpassword;

                $selectorUser = new Data\Specifier\Where($this->userModel, [
                    new Data\Specifier\WhereCheck("email", "==", $username)
                ]);

                $data = $this->db->select($this->userModel, null, $selectorUser);
                if ($data[0]["User-password"] != null) {
                    if ($data[0]["User-password"] == $password) {
                        $data[0]["User-password"] = null;
                    } else return $this->error();
                } else return $this->error();

                $selectorNaw = new Data\Specifier\Where($this->nawModel, [
                    new Data\Specifier\WhereCheck("id", "==", $data[0]['User-idnaw'])
                ]);
                $dataNaw = $this->db->select($this->nawModel, null, $selectorNaw);

                if(!$dataNaw[0]["Naw-id"]==null){
                    $dataNaw[0]["User-id"] = $data[0]["User-id"];
                    return print_r(json_encode($dataNaw[0]));
                } else return $this->error();
            } else return $this->error();
        }

        private function construct(){
            $this->userModel = new Data\FileModel("User");
            $this->nawModel = new Data\FileModel("Naw");
        }
        private function loadContent(){
            $content = $_POST['content'];
            $content = str_replace("-","",$content);
            $content = str_replace("\\","",$content);
            return (json_decode($content));
        }

        private function error(){
            $var['result']=false;
            return print_r(json_encode($var));

        }

        public function index(){
            //redirect to home
            $this->request->redirect();
        }

        /*
         * Function to check the static token for security reasons.
         */
        private function verifyCode($token) {
            $config = new Db_config();
            $secret = $config->getSecret();
            $finalhash1 = md5($secret);
            if($token==$finalhash1)return true;
            return false;
        }

    }