<?php

namespace Main;
use Auth\Account;
use Auth\Permission;

use BankAPI\Bank;

use DMF\Ext\Controller;
use DMF\Page;
use DMF\Data;
use Mailer\Mailer;

class ControllerIncludes extends Controller{

    protected $db;
    protected $input;
    protected $request;

    protected $bank;
    protected $mailer;

    protected $user;
    /**
     * @var Permission
     */
    protected $permission;

    protected $template;

    public function __construct(){
        parent::__construct();
        $config = new \Db_config();
        $this->db = new Data\MySQLDatabase($config->getHost(), $config->getUsername(), $config->getPw());
        $this->input = $this->app->getInputHandler();
        $this->request = $this->app->getRequestHandler();

        $this->template = new Page\Template('template');

        $this->user = new Account();



        $this->template->add("data_url", new Page\Text(
            $this->app->getRequestHandler()->getProtocol() . "://" .
            $this->app->getRequestHandler()->getHost() . '/Data/'));
        $this->getMenus();
        if(!$this->permission->canView($this->user)) return;

        $this->app->getPage()->add($this->template);
    }

    protected function getMenus(){
        if($this->user->isLoggedIn()) {
            $this->template->add("name", new Page\Text(
                    $this->user->getFirstName() . ' ' .
                    $this->user->getLastName()
                )
            );
            $this->template->add("extra", new Page\Text('<a href="/main/index/logout" class="logout">Uitloggen</a>', false));
            $this->template->add("extra", new Page\Text('<a href="/Main">Main</a>', false));
            if($this->user->getAccessLevel() > 2 ) {
                $this->template->add("extra", new Page\Text('<a href="/Beheer">Beheer</a>', false));
            }
        } else{
            $this->template->add("name", new Page\Text("Registreer"));
            $this->template->add("extra", new Page\Text('<a href="/main/index/login">Login</a>', false));
        }
    }

    protected function setTitle($title){
        $this->template->add("title", new Page\Text($title),false);
    }

}