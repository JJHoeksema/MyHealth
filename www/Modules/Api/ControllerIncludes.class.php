<?php

namespace Api;
use Auth\Permission;

use DMF\Ext\Controller;
use DMF\Data;
use DMF\Db_config;

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

    public function __construct(){
        parent::__construct();
        $config = new Db_config();
        $this->db = new Data\MySQLDatabase($config->getHost(), $config->getUsername(), $config->getPw());
        $this->input = $this->app->getInputHandler();
        $this->request = $this->app->getRequestHandler();
    }
}