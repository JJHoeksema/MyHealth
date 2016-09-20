<?php

namespace Beheer;


use Auth\Permission;
use DMF\App;
use DMF\Page;
use Main\ControllerIncludes;

class BeheerPermission extends ControllerIncludes {

    public function __construct(){
        $this->permission = new Permission(App::getInstance(), 6);
        parent::__construct();
        $this->template->add("menu", new Page\Template("Menus/beheer"));
    }

}