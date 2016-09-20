<?php
    namespace Beheer\Controller;

    use Beheer\BeheerPermission;
    use DateTime;
    use DMF\Data;
    use DMF\Page;

    class serviceController extends BeheerPermission{
        
        protected $serviceModel;
        protected $nawModel;

        public function __construct(){
            parent::__construct();
            $this->serviceModel = new Data\FileModel("Service");
            $this->nawModel = new Data\FileModel("Naw");
        }
        
        public function index(){
            $this->setTitle("Beheer | Service");

            $welcome = new Page\Template("beheerAttr/service/service");
            $welcome->add("id", new Page\Text($this->user->getPersonId()));
            $this->template->add("content", $welcome);
        }
        
        public function openstaande(){
            $this->setTitle("Openstaande aanvragen | CityPark");
            //Openstaande aanvragen
            $whereOpen = new Data\Specifier\Where($this->serviceModel, [new Data\Specifier\WhereCheck('medewerker', '==', $this->user->getPersonID()),
                                                                        new Data\Specifier\WhereCheck('status', '==', 0)]);

            $openQuery = $this->db->select($this->serviceModel,null, $whereOpen);
            if($openQuery!=NULL) {

                //Open titel
                $overzichtOpentitle = new Page\Template("beheerAttr/service/serviceOpentitle");
                $this->template->add("content", $overzichtOpentitle);

                foreach($openQuery AS $row) {
                    $overzichtOpen = new Page\Template("beheerAttr/service/serviceOpen");

                    if($row["Service-medewerker"]!=NULL){
                        $whereNaammedewerker = new Data\Specifier\Where($this->nawModel, new Data\Specifier\WhereCheck('id', '==',$row["Service-medewerker"]));
                        $medeQuery =  $this->db->select($this->nawModel,null, $whereNaammedewerker);
                        if(count($medeQuery) == 1){
                            $medeQuery = $medeQuery[0];
                        }else {
                            return;
                        }

                        $medewerker1= $medeQuery["Naw-naam"];
                        $medewerker2= $medeQuery["Naw-achternaam"];
                    }
                    else{
                        $medewerker1="Niet toegewezen";
                    }

                    $overzichtOpen->add("id", new Page\Text($row["Service-id"]) );
                    $overzichtOpen->add("vraag", new Page\Text($row["Service-vraag"]) );
                    $overzichtOpen->add("createdate", new Page\Text($row["Service-createdata"]) );
                    $overzichtOpen->add("titel", new Page\Text($row["Service-titel"]) );
                    $overzichtOpen->add("medewerker1", new Page\Text($medewerker1));
                    $overzichtOpen->add("medewerker2", new Page\Text($medewerker2));
                    $this->template->add("content", $overzichtOpen);
                }
                //Open end
                $overzichtOpenend = new Page\Template("beheerAttr/service/serviceOpenend");
                $this->template->add("content", $overzichtOpenend);
            }

            //Geen service
            if($closeQuery==NULL&&$openQuery==NULL){
                $overzichtleeg = new Page\Template("beheerAttr/service/serviceLeeg");
                $this->template->add("content", $overzichtleeg);
            }
        }
        
        public function afgehandeld(){
            $this->setTitle("Afgehandelde aanvragen | CityPark");
            //Afgehandelde aanvragen
            $whereClose = new Data\Specifier\Where($this->serviceModel, [new Data\Specifier\WhereCheck('medewerker', '==', $this->user->getPersonID()),
                                                                         new Data\Specifier\WhereCheck('status', '==', 1)]);
            $closeQuery = $this->db->select($this->serviceModel,null, $whereClose);
            if($closeQuery!=NULL) {

                //gesloten titel
                $overzichtClosetitle = new Page\Template("beheerAttr/service/serviceClosetitle");
                $this->template->add("content", $overzichtClosetitle);


                foreach($closeQuery AS $row) {
                    $overzichtOpgelost = new Page\Template("beheerAttr/service/serviceOpgelost");

                    if($row["Service-medewerker"]!=NULL){
                        $whereNaammedewerker = new Data\Specifier\Where($this->nawModel, new Data\Specifier\WhereCheck('id', '==',$row["Service-medewerker"]));
                        $medeQuery =  $this->db->select($this->nawModel,null, $whereNaammedewerker);
                        if(count($medeQuery) == 1){
                            $medeQuery = $medeQuery[0];
                        }else {
                            return;
                        }

                        $medewerker1= $medeQuery["Naw-naam"];
                        $medewerker2= $medeQuery["Naw-achternaam"];
                    }
                    else{
                        $medewerker1="Niet toegewezen";
                    }

                    $overzichtOpgelost->add("id", new Page\Text($row["Service-id"]) );
                    $overzichtOpgelost->add("vraag", new Page\Text($row["Service-vraag"]) );
                    $overzichtOpgelost->add("createdate", new Page\Text($row["Service-createdata"]) );
                    $overzichtOpgelost->add("titel", new Page\Text($row["Service-titel"]) );
                    $overzichtOpgelost->add("antwoord", new Page\Text($row["Service-antwoord"]) );
                    $overzichtOpgelost->add("medewerker1", new Page\Text($medewerker1) );
                    $overzichtOpgelost->add("medewerker2", new Page\Text($medewerker2) );
                    $overzichtOpgelost->add("enddate", new Page\Text($row["Service-afdata"]) );
                    $this->template->add("content", $overzichtOpgelost);
                }
                //Close end
                $overzichtCloseend = new Page\Template("beheerAttr/service/serviceCloseend");
                $this->template->add("content", $overzichtCloseend);
            }
        }
        
        public function inzien() {
            $this->setTitle("Beheer | CityPark");
            $whereID = new Data\Specifier\Where($this->serviceModel, [  new Data\Specifier\WhereCheck('medewerker', '==', $this->user->getPersonID()),
                                                                        new Data\Specifier\WhereCheck('id', '==', $this->input->get('id'))]);
            $serviceQuery=$this->db->select($this->serviceModel,null, $whereID);
            if(count($serviceQuery) == 1){
                $serviceQuery = $serviceQuery[0];
            }else {
                return;
            }


            if($serviceQuery["Service-id"]!=NULL) {
                if($serviceQuery["Service-medewerker"]!=NULL){
                    $whereNaammedewerker = new Data\Specifier\Where($this->nawModel, new Data\Specifier\WhereCheck('id', '==',$serviceQuery["Service-medewerker"]));
                    $medeQuery =  $this->db->select($this->nawModel,null, $whereNaammedewerker);
                    if(count($medeQuery) == 1){
                        $medeQuery = $medeQuery[0];
                    }else {
                        return;
                    }

                    $medewerker1= $medeQuery["Naw-naam"];
                    $medewerker2= $medeQuery["Naw-achternaam"];
                }
                else{
                    $medewerker1="Niet toegewezen";
                }
                if($serviceQuery['Service-status']==0) {
                    $serviceOpen = new Page\Template("beheerAttr/service/inzienOpen");
                    $serviceOpen->add("id", new Page\Text($serviceQuery["Service-id"]) );
                    $serviceOpen->add("vraag", new Page\Text($serviceQuery["Service-vraag"]) );
                    $serviceOpen->add("createdate", new Page\Text($serviceQuery["Service-createdata"]) );
                    $serviceOpen->add("titel", new Page\Text($serviceQuery["Service-titel"]) );
                    $serviceOpen->add("medewerker1", new Page\Text($medewerker1));
                    $serviceOpen->add("medewerker2", new Page\Text($medewerker2));

                    $this->template->add("content", $serviceOpen);
                }
                elseif($serviceQuery['Service-status']==1){
                    $servicec = new Page\Template("beheerAttr/service/inzienClose");
                    $servicec->add("id", new Page\Text($serviceQuery["Service-id"]) );
                    $servicec->add("vraag", new Page\Text($serviceQuery["Service-vraag"]) );
                    $servicec->add("createdate", new Page\Text($serviceQuery["Service-createdata"]) );
                    $servicec->add("titel", new Page\Text($serviceQuery["Service-titel"]) );
                    $servicec->add("antwoord", new Page\Text($serviceQuery["Service-antwoord"]) );
                    $servicec->add("medewerker1", new Page\Text($medewerker1) );
                    $servicec->add("medewerker2", new Page\Text($medewerker2) );
                    $servicec->add("enddate", new Page\Text($serviceQuery["Service-afdata"]) );

                    $this->template->add("content", $servicec);


                }
                else{
                    $serviceNietgeldig = new Page\Template("beheerAttr/service/inzienOngeld");
                    $this->template->add("content", $serviceNietgeldig);
                }
            }
            else{
                $serviceNietgeldig = new Page\Template("beheerAttr/service/inzienOngeld");
                $this->template->add("content", $serviceNietgeldig);
            }
        }
        
        public function beantwoorden() {
            $this->setTitle("Sluiten | CityPark");
            var_dump($this->input->allArgs());
            if($this->input->arg(0)!=NULL){
                
                $whereID = new Data\Specifier\Where($this->serviceModel, [ new Data\Specifier\WhereCheck('id', '==', $this->input->arg(0))]);
                $epoch = time()+60*60;
                $dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
                $data = [
                    'status' => 1,
                    'afdata' => $dt->format('Y-m-d H:i:s'),
                    'antwoord' => $this->input->post('antwoord')
                ];
                var_dump($data);
                $this->db->update($this->serviceModel,$data,$whereID);
                echo 'test';
            }
            $this->request->redirect("Beheer/service/");

        }

    }