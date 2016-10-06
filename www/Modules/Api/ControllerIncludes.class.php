<?php

namespace Api;

use DMF\Ext\Controller;
use DMF\Data;
use DMF\Db_config;
use DMF\Page;

class ControllerIncludes extends Controller{

    protected $db;
    protected $request;
    protected $input;
    protected $template;
    public function __construct(){
        parent::__construct();
        $config = new Db_config();
        $this->db = new Data\MySQLDatabase($config->getHost(), $config->getUsername(), $config->getPw());
        $this->input = $this->app->getInputHandler();
        $this->request = $this->app->getRequestHandler();

        $this->template = new Page\Template('template');
        $this->template->add("data_url", new Page\Text(
            $this->app->getRequestHandler()->getProtocol() . "://" .
            $this->app->getRequestHandler()->getHost() . '/Data/'));


        $this->app->getPage()->add($this->template);
    }
}