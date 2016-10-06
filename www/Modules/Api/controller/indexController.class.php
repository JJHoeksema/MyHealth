<?php
    namespace Api\Controller;

    use Api\NoPermission;
    use DMF\Data;
    use DMF\App;
    use DMF\Db_config;

    /*
     * Android API class
     */

    class indexController extends NoPermission{
        private $userModel;
        private $nawModel;
        private $measurementsModel;

        /*
         * API fucntion to delete a measurement
         * @param host/api/index/measurements/token/id
         * @return boolean
         */
        public function deleteMeasurements(){
            App::getInstance()->getPage()->clear();
            if($this->verifyCode($this->input->arg(0))) {
                $this->construct();

                $selector = new Data\Specifier\Where($this->measurementsModel, [
                    new Data\Specifier\WhereCheck("id", "==", $this->input->arg(1))
                ]);
                $this->db->delete($this->measurementsModel, $selector);

                return print_r(json_encode($var['result']=true));

            } else return $this->error();
        }

        /*
         * API fucntion to get all the measurements
         * @param host/api/index/measurements/token/userid
         * @return all the measurements of the user id
         */
        public function measurements(){
            App::getInstance()->getPage()->clear();
            if($this->verifyCode($this->input->arg(0))) {
                $id = $this->input->arg(1);
                $this->construct();

                //query
                $selector = new Data\Specifier\Where($this->measurementsModel, [
                    new Data\Specifier\WhereCheck("user_id", "==", $id)
                ]);
                $data = $this->db->select($this->measurementsModel, null, $selector);

                if($data[0]["Readings-id"]!=null){
                    return print_r(json_encode($data));
                } else return $this->error();

            } else return $this->error();
        }

        /*
         * API fucntion to get all the measurements
         * @param host/api/index/login/token
         * @param POST username and hashed password
         * @return naw object + userid
         */
        public function login() {
            App::getInstance()->getPage()->clear();

            if($this->verifyCode($this->input->arg(0))) {
                $this->construct();
                $content = $this->loadContent();

                //query
                $selectorUser = new Data\Specifier\Where($this->userModel, [
                    new Data\Specifier\WhereCheck("email", "==", $content->Useremail)
                ]);
                $data = $this->db->select($this->userModel, null, $selectorUser);

                if ($data[0]["User-password"] != null) {
                    if ($data[0]["User-password"] == $content->Userpassword) {
                        $data[0]["User-password"] = null;
                    } else return $this->error();

                } else return $this->error();

                //query
                $selectorNaw = new Data\Specifier\Where($this->nawModel, [
                    new Data\Specifier\WhereCheck("id", "==", $data[0]['User-idnaw'])
                ]);
                $dataNaw = $this->db->select($this->nawModel, null, $selectorNaw);

                if($dataNaw[0]["Naw-id"]!=null){
                    $dataNaw[0]["User-id"] = $data[0]["User-id"];
                    return print_r(json_encode($dataNaw[0]));
                } else return $this->error();

            } else return $this->error();
        }

        //construct database models
        private function construct(){
            $this->userModel = new Data\FileModel("User");
            $this->nawModel = new Data\FileModel("Naw");
            $this->measurementsModel = new Data\FileModel("Measurements");
        }

        //load the post content and strip of escapes
        private function loadContent(){
            $content = str_replace("-","",$_POST['content']);
            $content = str_replace("\\","",$content);
            return (json_decode($content));
        }

        //show false object
        private function error(){
            return print_r(json_encode($var['result']=false));
        }

        //redirect to home
        public function index(){
            $this->request->redirect();
        }

        /*
         * Function to check the static token for security reasons.
         * @param token based on md5 hash of secret
         * @return boolean
         */
        private function verifyCode($token) {
            $config = new Db_config();
            if($token==md5($config->getSecret()))return true;
            return false;
        }

    }