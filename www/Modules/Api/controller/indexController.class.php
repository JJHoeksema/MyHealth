<?php
    namespace Api\Controller;

    use Api\NoPermission;
    use DMF\Data;
    use DMF\App;
    use DMF\Db_config;
    class indexController extends NoPermission{
        private $userModel;

        public function login($token = 1) {
            App::getInstance()->getPage()->clear();
            $content = $this->input->post("content");
            if($this->verifyCode($token, $content)) {
                //$username = $this->input->post("username");
                //$password = $this->input->post("password");
                $username = "tat@test.nl";
                $password = '$2y$12$0oWU7uFT7wORbf42plT4T.zKPIJ1kmA6.bafDImEXpOCE/IjkRIA6';

                $this->userModel = new Data\FileModel("User");

                $selector = new Data\Specifier\Where($this->userModel, [
                    new Data\Specifier\WhereCheck("email", "==", $username),
                ]);

                $data = $this->db->select($this->userModel, null, $selector);
                if($data[0]["User-password"] != null) {
                    str_replace("\\","",$data[0]["User-password"]);
                    if ($data[0]["User-password"] == $password){
                        $data[0]["User-password"]=null;
                        print_r(json_encode($data[0]));}
                    else return $this->error();
                }
                else return $this->error();
            } else {
                return $this->error();
            }
        }

        private function error(){
            $var['result']=false;
            return print_r(json_encode($var));

        }

        public function index(){
            $this->request->redirect();
        }

        private function verifyCode($token, $content) {
            $config = new Db_config();
            $secret = $config->getSecret();

            $time = new \DateTime("NOW", new \DateTimeZone('Europe/Amsterdam'));
            $stamp = $time->format('YMDhi');
            $time->add(new \DateInterval('PT' . 1 . 'M'));
            $stamp2 = $time->format('YMDhi');

            $contenthash = md5($content);
            $finalhash1 = md5($stamp.$secret.$contenthash);
            $finalhash2 = md5($stamp2.$secret.$contenthash);

            if($token===$finalhash1||$token===$finalhash2)return true;
            return false;
        }

    }