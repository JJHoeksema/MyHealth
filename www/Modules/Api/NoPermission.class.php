<?php

namespace Api;


use Auth\Permission;
use DMF\App;
use DMF\Page;

class NoPermission extends ControllerIncludes {

    public function __construct(){
        $this->permission = new Permission(App::getInstance());
        parent::__construct();
    }

}