<?php

namespace Auth;

use DMF\_Static;
use DMF\Data;

class Account {
    private $session;
    private $db;
    private $sessionModel;
    private $userModel;
    private $lockModel;
    private $timeoutTime;
    private static $loggedIn;

    public function __construct(){
        $this->db           =   new Data\MySQLDatabase("localhost", "root", "root");
        $this->session      =   new Data\SessionData();
        $this->userModel    =   new Data\FileModel("User");
        $this->lockModel    =   new Data\FileModel("Login_Lock");
        $this->sessionModel =   new Data\FileModel("account", false);
        $this->timeoutTime  =   60*10; //10 minutes

        //check if is logged in currently
        if(Account::$loggedIn != null) return;
        $result = $this->session->select($this->sessionModel);
        if(count($result) == 0){
            Account::$loggedIn = false;
            return;
        };
        $result = $result[0];
        if($result['timestamp_end'] < time()){
            Account::$loggedIn = false;
            $this->session->clear($this->sessionModel);
            return;
        }
        $passwd = $result['password'];
        $selector = new Data\Specifier\Where($this->userModel, new Data\Specifier\WhereCheck('email', '==', $result['username']));
        $result = $this->db->select($this->userModel, null, $selector);
        if(count($result) == 0){
            Account::$loggedIn = false;
            $this->session->clear($this->sessionModel);
            return;
        }
        $this->setSessionData($result[0]['User-email']);

        if($result[0]['User-password'] != $passwd) {
            Account::$loggedIn = false;
            $this->session->clear($this->sessionModel);
            return;
        }
        Account::$loggedIn = true;

    }

    private function setSessionData($username, $update = true){
        $selector = new Data\Specifier\Where($this->userModel,new Data\Specifier\WhereCheck('email', '==', $username));
        $result = $this->db->multiselect([$this->userModel, new Data\FileModel('Naw'), new Data\FileModel('Functie')], null, $selector)[0];

        $data["id"] = $result['User-id'];
        $data["id_person"] = $result['User-idnaw'];
        $data["id_group"] = $result['User-idfunctie'];
        $data["username"] = $result['User-email'];
        $data["password"] = $result['User-password'];
        $data["firstName"] = $result['Naw-naam'];
        $data["lastName"] = "";
        $data["lastName"] .= $result['Naw-achternaam'];
        $data["groupName"] = $result['Functie-naam'];
        $data["accessLevel"] = $result['Functie-niveau'];
        if($update){
            unset($data['id']);
            unset($data['username']);
            $selector = new Data\Specifier\Where($this->sessionModel, new Data\Specifier\WhereCheck('username', '==', $username));
            $this->session->update($this->sessionModel, $data, $selector);
           return;
        }

        $data["timestamp_start"] = time();
        $data["timestamp_end"] = time() + $this->timeoutTime;
        $this->session->clear($this->sessionModel);
        $this->session->insert($this->sessionModel, $data);
    }

    /**
     * Method to get the user ID of the logged in user
     * @return int user id (-1 if not logged in)
     */
    public function getID(){
        if (!$this->isLoggedIn()) return -1;
        $result = $this->session->select($this->sessionModel)[0];
        return intval($result['id']);
    }

    public function verify($key, $date){
        $date = str_replace("T", " ", $date);
        $selector = new Data\Specifier\Where($this->userModel, [
            new Data\Specifier\WhereCheck("verify", "==", $key),
            new Data\Specifier\WhereCheck("createdate", "==", $date),
        ]);
        if(count($this->db->select($this->userModel, null, $selector)) != 1) return false;
        $this->db->update($this->userModel, ["verify" => null, "verified" => 1], $selector);
        return true;
    }

    /**
     * Method to get the user ID of the logged in user
     * @return int user id (-1 if not logged in)
     */
    public function getGroupID(){
        if (!$this->isLoggedIn()) return -1;
        $result = $this->session->select($this->sessionModel)[0];
        return intval($result['id_group']);
    }

    /**
     * Method to get the person ID of the logged in user
     * @return int person id (-1 if not logged in)
     */
    public function getPersonID(){
        if (!$this->isLoggedIn()) return -1;
        $result = $this->session->select($this->sessionModel)[0];
        return intval($result['id_person']);
    }

    /**
     * Method to get the username of the logged in user
     * @return null|string username (null if not logged in)
     */
    public function getUsername(){
        if(!$this->isLoggedIn()) return null;
        $result = $this->session->select($this->sessionModel)[0];
        return $result['username'];
    }

    /**
     * Method to get the first name of the logged in user
     * @return null|string first name (null if not logged in)
     */
    public function getFirstName(){
        if(!$this->isLoggedIn()) return null;
        $result = $this->session->select($this->sessionModel)[0];
        return $result['firstName'];
    }

    /**
     * Method to get the last name of the logged in user
     * @return null|string last name (null if not logged in)
     */
    public function getLastName(){
        if(!$this->isLoggedIn()) return null;
        $result = $this->session->select($this->sessionModel)[0];
        return $result['lastName'];
    }

    /**
     * Method to get the group name of the logged in user
     * @return null|string group name (null if not logged in)
     */
    public function getGroupName(){
        if(!$this->isLoggedIn()) return null;
        $result = $this->session->select($this->sessionModel)[0];
        return $result['groupName'];
    }

    /**
     * Method to get the AccessLevel of the logged in user
     * @return int accessLevel (-1 if not logged in)
     */
    public function getAccessLevel(){
        if (!$this->isLoggedIn()) return -1;
        $result = $this->session->select($this->sessionModel)[0];
        return intval($result['accessLevel']);
    }


    public function isLoggedIn(){
        return Account::$loggedIn;
    }

    public function refresh(){
        if(!$this->isLoggedIn()) return false;
        $result = $this->session->select($this->sessionModel)[0];
        $selector = new Data\Specifier\Where($this->sessionModel, new Data\Specifier\WhereCheck('username', '==', $result['username']));
        return $this->session->update($this->sessionModel, ['timestamp_end' => (time() + $this->timeoutTime)], $selector);
    }

    public function logOut(){
        $this->session->clear($this->sessionModel);
        Account::$loggedIn = false;
    }

    public function isVerified($username){
        $selector = new Data\Specifier\Where($this->userModel, new Data\Specifier\WhereCheck('email', '==', $username));
        $result = $this->db->select($this->userModel, null, $selector);
        if(count($result) != 1) return null;
        if($result['User-verified'] != 1) return false;
        return true;
    }

    private function getIP() {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        }
        else {
            $ip = $remote;
        }

        return $ip;
    }

    private function checkIPAndLock(){
        if($this->ip_is_locked()) return;
        $_5MinAgo = (new \DateTime())->sub(new \DateInterval("PT5M"));
        $_15MinInFuture = (new \DateTime())->add(new \DateInterval("PT15M"));
        $selector = new Data\Specifier\Where(new Data\FileModel("User_Log"), [
            new Data\Specifier\WhereCheck("IP", "==", $this->getIP()),
            new Data\Specifier\WhereCheck("geslaagd", "==", 0),
            new Data\Specifier\WhereCheck("datum", ">", $_5MinAgo->format("Y-m-d H:i:s"))
        ]);
        $result = $this->db->select(new Data\FileModel("User_Log"), null, $selector);
        if(count($result) > 15){
            $data = [];
            $data['type'] = 'ip';
            $data['waarde'] = $this->getIP();
            $data['lockeind'] = $_15MinInFuture->format("Y-m-d H:i:s");
            $this->db->insert($this->lockModel, $data);
        }
    }

    private function checkAndLock($userid, $username){
        if($this->user_is_locked($username)) return;
        $_5MinAgo = (new \DateTime())->sub(new \DateInterval("PT5M"));
        $_15MinInFuture = (new \DateTime())->add(new \DateInterval("PT15M"));
        $selector = new Data\Specifier\Where(new Data\FileModel("User_Log"), [
            new Data\Specifier\WhereCheck("iduser", "==", $userid),
            new Data\Specifier\WhereCheck("geslaagd", "==", 0),
            new Data\Specifier\WhereCheck("datum", ">", $_5MinAgo->format("Y-m-d H:i:s"))
        ]);
        $result = $this->db->select(new Data\FileModel("User_Log"), null, $selector);
        if(count($result) > 5){
            $data = [];
            $data['type'] = 'email';
            $data['waarde'] = $username;
            $data['lockeind'] = $_15MinInFuture->format("Y-m-d H:i:s");
            $this->db->insert($this->lockModel, $data);
        }
    }

    private function log_login($userId, $valid = false) {
        $data = [];
        $data['datum'] = _Static\Time::getTimestamp('Europe/Amsterdam');
        $data['iduser'] = $userId;
        $data['geslaagd'] = ($valid == true)? 1 : 0;
        $data['IP'] = $this->getIP();

        $this->db->insert(new Data\FileModel("User_Log"), $data);
    }

    public function user_is_locked($username) {
        $selector = new Data\Specifier\Where($this->lockModel, [
            new Data\Specifier\WhereCheck("type", "==", "email"),
            new Data\Specifier\WhereCheck("waarde", "==", $username),
            new Data\Specifier\WhereCheck("lockeind", ">", _Static\Time::getTimestamp('Europe/Amsterdam'))
        ]);
        $result = $this->db->select($this->lockModel, null, $selector);
        if(count($result) == 0) return false;
        return true;
    }

    public function ip_is_locked(){
        $ip = $this->getIP();
        $selector = new Data\Specifier\Where($this->lockModel, [
            new Data\Specifier\WhereCheck("type", "==", "ip"),
            new Data\Specifier\WhereCheck("waarde", "==", $ip),
            new Data\Specifier\WhereCheck("lockeind", ">", _Static\Time::getTimestamp('Europe/Amsterdam'))
        ]);
        $result = $this->db->select($this->lockModel, null, $selector);

        if(count($result) == 0) return false;
        return true;
    }

    public function login($username, $password){
        if ($this->ip_is_locked()) {$this->log_login(0); $this->checkIPAndLock(); return false;}
        $selector = new Data\Specifier\Where($this->userModel, new Data\Specifier\WhereCheck('email', '==', $username));
        $result = $this->db->multiselect([$this->userModel, new Data\FileModel('Naw'), new Data\FileModel('Functie')], null, $selector);
        if (count($result) != 1){
            $this->log_login($result['User-id']);
            $this->checkIPAndLock();
            return false;
        }
        $result = $result[0];
        if($this->user_is_locked($username)) return false;
        if (!password_verify ($password , $result['User-password'])){
            $this->log_login($result['User-id']);
            $this->checkIPAndLock();
            $this->checkAndLock($result['User-id'], $username);
            return false;
        }
        if($result['User-verified'] != 1) return false;

        $this->setSessionData($result['User-email'], false);
        $this->db->update($this->userModel, ['lastlog' => _Static\Time::getTimestamp('Europe/Amsterdam')], $selector);
        $this->log_login($result['User-id'], true);
        return Account::$loggedIn = true;
    }

    /**
     * Method to check if the user exists
     * @param string $email the username of the user
     * @return bool
     */
    public function userExist($email){
        $selector = new Data\Specifier\Where($this->userModel,new Data\Specifier\WhereCheck('email', '==', $email));
        $result = $this->db->select($this->userModel, null, $selector);
        if(count($result) > 0) return true;
        return false;
    }

    /**
     * Method to check if the person has an account
     * @param int $idPerson id of person to check
     * @return bool
     */
    public function hasAccount($idPerson){
        $selector = new Data\Specifier\Where($this->userModel,new Data\Specifier\WhereCheck('idfunctie', '==', $idPerson));
        $result = $this->db->select($this->userModel, null, $selector);
        if(count($result) > 0) return true;
        return false;
    }


    /**
     * Method to register a new user
     * @param string $email
     * @param string $password password given by the user
     * @param string $email
     * @param int $idPerson
     * @param int $idGroup default is customer
     * @return null|string validation key (null if registration failed)
     */
    public function register($email, $password, $idPerson, $idGroup = 10){
        $data = [];
        $data['verify']                 = _Static\Random::string(10);
        $data['createdate']             = _Static\Time::getTimestamp('Europe/Amsterdam');
        $data['password']               = password_hash($password, CRYPT_BLOWFISH,
                                                ['cost' => 12]);
        $data['idnaw']                  = $idPerson;
        $data['idfunctie']              = $idGroup;
        $data['email']                  = $email;
        $data['verified']               = false;

        if( $this->userExist($email) ||
            $this->hasAccount($idPerson)){
            return null;
        }

        // Okay everything is checked, no issues so far, insert into database
        $result = $this->db->insert($this->userModel, $data);
        if (count($result) == 0) {
            return null;
        }
        return ["key" => $data['verify'], "date" => str_replace(" ", "T", $data['createdate'])];
    }
}