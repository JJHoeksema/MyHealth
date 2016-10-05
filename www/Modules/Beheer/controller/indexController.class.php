<?php
    namespace Beheer\Controller;

    use Beheer\BeheerPermission;
    use DMF\Page;
    use DMF\Data;

    class indexController extends BeheerPermission{
        private $measureModel;

        public function index(){
            $this->setTitle("Beheer | home");

            $welcome = new Page\Template("beheerAttr/welcome");
            $this->template->add("content", $welcome);

            $welcome->add("name", new Page\Text($this->user->getFirstName()));
        }

        public function measurements() {
            $this->measureModel    =   new Data\FileModel("Measurements");

            $this->setTitle("Beheer | Measurements");
            $measurements = new Page\Template("beheerAttr/measurementsOverzicht");
            $measurements->add("name", new Page\Text($this->user->getFirstName()));

            $selector = new Data\Specifier\Where($this->measureModel, [
                new Data\Specifier\WhereCheck("user_id", "==", $this->user->getID()),
            ]);

            $result = $this->db->select($this->measureModel, null, $selector);
            if(count($result) == 0){
                $measurements->add("content", new Page\Text("Er zijn geen measurements gemaakt."));
            }

            foreach($result AS $row) {
                $readings = new Page\Template("/beheerAttr/measurements");
                $readings->add("id", new Page\Text($row['Readings-id']));
                $readings->add("type", new Page\Text($row['Readings-type']));
                $readings->add("naam", new Page\Text($row['Readings-naam']));
                $readings->add("value", new Page\Text($row['Readings-value']));
                $measurements->add("content", $readings);
            }
            $this->template->add("content", $measurements);


        }

    }