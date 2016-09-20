<?php

namespace Main;


use Auth\Permission;
use DMF\App;
use DMF\Page;

class LoggedInPermission extends ControllerIncludes {

    public function __construct(){
        $this->permission = new Permission(App::getInstance(), 0);
        parent::__construct();
        $this->template->add("menu", new Page\Template("Menus/main"));
        $this->template->add("menu", new Page\Template("Menus/logged-in"));
    }

}