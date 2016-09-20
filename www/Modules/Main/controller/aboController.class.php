<?php
    namespace Main\Controller;

    use Main\LoggedInPermission;
    use DMF\Page;
    use DMF\Data;
    use DateTime;
    use BankAPI;
//todo pas exception
    class aboController extends LoggedInPermission{
        protected $abonnementModel;
        protected $nawModel;
        protected $plekModel;
        protected $pasModel;
        protected $abo_tariefModel;

        public function __construct(){
            parent::__construct();
            $this->abonnementModel = new Data\FileModel("Abonnement");
            $this->nawModel = new Data\FileModel("Naw");
            $this->plekModel = new Data\FileModel("Plek");
            $this->pasModel = new Data\FileModel("Pas");
            $this->abo_tariefModel = new Data\FileModel("Abo_Tarief");
        }

        public function index(){
            //doorzetten naar overzicht
            $this->Overzicht();
        }
        public function Overzicht(){
            $this->setTitle("Abonnement | CityPark");

            //query met abo's
            $whereSelect = new Data\Specifier\Where($this->abonnementModel, [   new Data\Specifier\WhereCheck('iduser', '==', $this->user->getPersonID())]);
            $queryAb = $this->db->select($this->abonnementModel,null, $whereSelect);

            //pagina layout
            $hoofdTemplate = new Page\Template("mainAttr/abo/hoofdTemplate");
            $activeAbos = (new Page\Template("mainAttr/group-div"))->add("Title", new Page\Text("Actieve Abonnementen"));
            $blockedAbos = (new Page\Template("mainAttr/group-div"))->add("Title", new Page\Text("Geblokeerde Abonnementen"));
            $endedAbos = (new Page\Template("mainAttr/group-div"))->add("Title", new Page\Text("Beeindigde Abonnementen"));

            //rijen voor abonnementen maken
            foreach($queryAb AS $row) {
                if ($row['Abonnement-status']==1) {
                    $activeRow = new Page\Template("mainAttr/abo/status1");
                    if($row['Abonnement-einddatum']==NULL){
                        $activeRow->add('enddate',new Page\Text("U heeft geen einddatum ingesteld.") );
                    } else {
                        $activeRow->add('enddate', new Page\Text($row['Abonnement-einddatum']));
                    }
                    $activeRow->add('startdate', new Page\Text($row['Abonnement-startdatum']));
                    $activeRow->add('id', new Page\Text($row['Abonnement-id']));
                    $activeAbos->add('content', $activeRow);
                    $active = true;

                } elseif ($row['Abonnement-status']== 2) {
                    $blockedRow = new Page\Template("mainAttr/abo/status2");
                    $blockedRow->add('startdate', new Page\Text($row['Abonnement-startdatum']));
                    $blockedRow->add('enddate', new Page\Text($row['Abonnement-einddatum']));
                    $blockedRow->add('id', new Page\Text($row['Abonnement-id']));
                    $blockedAbos->add('content', $blockedRow);
                    $blocked = true;

                } elseif ($row['Abonnement-status']== 3) {
                    $endedRow = new Page\Template("mainAttr/abo/status3");
                    $endedRow->add('enddate', new Page\Text($row['Abonnement-einddatum']));
                    $endedRow->add('startdate', new Page\Text($row['Abonnement-startdatum']));
                    $endedRow->add('id', new Page\Text($row['Abonnement-id']));
                    $endedAbos->add('content', $endedRow);
                    $ended= true;


                }
            }

            //Rijen toevoegen als deze gebruikt zijn
            if($active==true) {
                $hoofdTemplate->add("content", $activeAbos);
            }
            if($blocked==true) {
                $hoofdTemplate->add("content", $blockedAbos);
            }
            if($ended==true) {
                $hoofdTemplate->add("content", $endedAbos);
            }
            //errors displayen
            if($this->input->get('er')==1) {
                $hoofdTemplate->add('error', new Page\Text("Er is iets mis gegaan!"));
            }

            //toevoegen aan hoofdscherm
            $this->template->add("content", $hoofdTemplate);

        }

        public function newAb(){
            $this->setTitle("Nieuw Abonnement | CityPark");
            $newAbbo = new Page\Template("mainAttr/abo/new");

            //error's displayen
            if($this->input->get('er')==1){
                $newAbbo->add('error', new Page\Text("Er zijn niet meer genoeg plekken"));
            } elseif($this->input->get('er')==2){
                $newAbbo->add('error', new Page\Text("Uw Bankkrediet is te laag"));
            } elseif($this->input->get('er')==3){
                $newAbbo->add('error', new Page\Text("Er is iets mis gegaan"));
            }


            $this->template->add("content", $newAbbo);
        }

        public function newAbcheck(){
            $this->setTitle("Checking | CityPark");

            //plekken van abonee's tellen
            $whereSelect = new Data\Specifier\Where($this->abonnementModel, [   new Data\Specifier\WhereCheck('status', '==', 1)]);
            $countQuery = $this->db->select($this->abonnementModel, NULL, $whereSelect);
            $count = count($countQuery);
            //bankcheck
            $userSelect = new Data\Specifier\Where($this->nawModel, [   new Data\Specifier\WhereCheck('id', '==', $this->user->getPersonID())]);
            $userQuery = $this->db->select($this->nawModel, NULL, $userSelect);
            $bank = new BankAPI\Bank("citypark.marwijnn.me:8080/cp-bank/BankService", "MY-SUPER-SECRET-API-KEY", "NL38CPBK0000100000");


            $wheredate = new Data\Specifier\Where($this->abo_tariefModel, [
                new Data\Specifier\WhereCheck('datumbegin', '<=', $this->input->get('start')),
                new Data\Specifier\WhereCheck('datumeind', '>=', $this->input->get('start'))
            ]);
            $wheredate->orWhere([
                new Data\Specifier\WhereCheck('datumbegin', '<=', $this->input->get('start')),
                new Data\Specifier\WhereCheck('datumeind', '==', NULL)]);
            $selectTarief = $this->db->select($this->abo_tariefModel, NULL, $wheredate);

            $bedrag = $selectTarief[0]["Abo_Tarief-tarief"] * 6;
            $bankcheck = $bank->hasEnoughCredit($userQuery[0]["Naw-reknummer"], $bedrag);
            //checks op bank en plekken
            if($count<=150){

                if($bankcheck==true){

                    $this->request->redirect("main/abo/newAbcon/?start=".$this->input->get('start')."&end=".$this->input->get('end')."");
                }
                else{
                    $this->request->redirect("main/abo/newAb/?er=2");
                }
            }
            else{
                $this->request->redirect("main/abo/newAb/?er=1");
            }

        }

        public function newAbcon(){
            $this->setTitle("Confirming | CityPark");
            //check of deze aanvraag klopt en daarna invoeren
            if($this->user->getPersonID()!=NULL||$this->input->get('startdate')!=NULL) {

                //Plek updaten
                $plekSelect = new Data\Specifier\Where($this->plekModel, [new Data\Specifier\WhereCheck('status', '==', 1),]);
                $queryPlek = $this->db->select($this->plekModel, NULL, $plekSelect);
                $dataPlek = [
                    'status' => 2
                ];
                $plekSelect2 = new Data\Specifier\Where($this->plekModel, [new Data\Specifier\WhereCheck('id', '==', $queryPlek[0]['Plek-id'])]);
                $this->db->update($this->plekModel, $dataPlek, $plekSelect2);

                //abbo updaten/insterten
                if($this->input->get('end')==NUll){
                    $data = [
                        'iduser' => $this->user->getPersonID(),
                        'startdatum' => $this->input->get('start'),
                        'status' => 1,
                        'plek' => $queryPlek[0]['Plek-id']
                    ];
                } else{
                    $data = [
                        'iduser' => $this->user->getPersonID(),
                        'startdatum' => $this->input->get('start'),
                        'einddatum' => $this->input->get('end'),
                        'status' => 1,
                        'plek' => $queryPlek[0]['Plek-id']
                    ];
                }

                $this->db->insert($this->abonnementModel, $data);

                //passen updaten
                $pasSelect = new Data\Specifier\Where($this->pasModel, [
                    new Data\Specifier\WhereCheck('status', '==', 1),
                    new Data\Specifier\WhereCheck('idsoort', '==', 1),
                    new Data\Specifier\WhereCheck('idabbo', '==', NULL)]);

                $bezoekpasSelect = new Data\Specifier\Where($this->pasModel, [
                    new Data\Specifier\WhereCheck('status', '==', 1),
                    new Data\Specifier\WhereCheck('idsoort', '==', 2),
                    new Data\Specifier\WhereCheck('idabbo', '==', NULL)]);

                $pasQuery = $this->db->select($this->pasModel, NULL, $pasSelect);
                $pasSelect2 = new Data\Specifier\Where($this->pasModel, [new Data\Specifier\WhereCheck('id', '==', $pasQuery[0]['Pas-id'])]);
                $bezoekpasQuery = $this->db->select($this->pasModel, NULL, $bezoekpasSelect);
                $bezoekpasSelect2 = new Data\Specifier\Where($this->pasModel, [new Data\Specifier\WhereCheck('id', '==', $bezoekpasQuery[0]['Pas-id'])]);
                $whereAbo = new Data\Specifier\Where($this->abonnementModel, [
                    new Data\Specifier\WhereCheck('iduser', '==', $this->user->getPersonID()),
                    new Data\Specifier\WhereCheck('plek', '==', $queryPlek[0]['Plek-id'])]);
                $selectAbo = $this->db->select($this->abonnementModel, NULL, $whereAbo);
                $abo = $selectAbo[0]['Abonnement-id'];

                $dataPas = [
                    'idabbo' => $abo,
                    'set'=> 1,
                    'status'=> 1

                ];
                $this->db->update($this->pasModel, $dataPas, $bezoekpasSelect2);
                $this->db->update($this->pasModel, $dataPas, $pasSelect2);

                //redir
                $this->request->redirect("main/abo/overzicht");

            }else{
                $this->request->redirect("main/abo/newAb/?er=3");
            }
        }


        public function opzeggencon(){
            $this->setTitle("Opzeggen | CityPark");
            //query om abbo op te halen
            $whereSelect = new Data\Specifier\Where($this->abonnementModel, [   new Data\Specifier\WhereCheck('iduser', '==', $this->user->getPersonID()),
                                                                                new Data\Specifier\WhereCheck('id', '==', $this->input->get('id'))]);
            $selectQuery = $this->db->select($this->abonnementModel, NULL, $whereSelect);
            //check op actief abbo
            if($selectQuery[0]['Abonnement-status']==1) {
                //enddate zetten
                $epoch = time()+60*60;
                $dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
                $data = [
                    'status' => 3,
                    'einddatum'=> $dt->format('Y-m-d')
                ];
                $this->db->update($this->abonnementModel, $data, $whereSelect);

                //plek vrijmaken
                $dataPlek=[
                    'status'=>1
                ];
                $plekSelect = new Data\Specifier\Where($this->plekModel, [new Data\Specifier\WhereCheck('id', '==', $selectQuery[0]['Abonnement-plek'])]);
                $this->db->update($this->plekModel, $dataPlek, $plekSelect);

                //passen vrijmaken
                $wherePas = new Data\Specifier\Where($this->pasModel, [new Data\Specifier\WhereCheck('idabbo', '==', $this->input->get('id'))]);
                $dataPas = [
                    'idabbo' => NULL,
                    'set' =>0,
                    'status'=> 2
                ];
                $this->db->update($this->pasModel, $dataPas, $wherePas);

                $this->request->redirect("main/abo/overzicht");
            } else {

                $this->request->redirect("main/abo/overzicht/?er=1");
            }


        }

    }