<?php
    namespace Beheer\Controller;

    use Beheer\BeheerPermission;
    use DMF\Page;
    use DMF\Data;

    class indexController extends BeheerPermission{

        public function index(){
            $this->setTitle("Beheer | home");

            $welcome = new Page\Template("beheerAttr/welcome");
            $this->template->add("content", $welcome);

            $welcome->add("name", new Page\Text($this->user->getFirstName()));
        }

    }