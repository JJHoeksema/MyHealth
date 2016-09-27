<?php
    namespace Api\Controller;

    use Api\NoPermission;
    use DMF\Data;

    class indexController extends NoPermission{
        private $userModel;

        public function login($token) {
            $token = 1;
            if($this->verifyCode($token)) {
                //$username = $this->input->post("username");
                //$password = $this->input->post("password");
                $username = "tant@test.nl";
                $password = "test";

                $this->userModel = new Data\FileModel("User");

                $selector = new Data\Specifier\Where($this->userModel, [
                    new Data\Specifier\WhereCheck("username", "==", $username),
                ]);

                $data = $this->db->select($this->userModel, null, $selector);
                echo $data;
                if($data->password == $password);
                    return $data;

            } else {
                return "bad request";
            }
        }

        public function verifyCode($token) {
            return true;
        }

    }