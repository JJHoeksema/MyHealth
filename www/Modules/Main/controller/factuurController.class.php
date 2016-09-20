<?php
    namespace Main\Controller;

    use DMF\Events\StatusChangeEvent;
    use Main\LoggedInPermission;
    use DMF\Page;
    use DMF\Data;
    use PDF;
    use Generator;

    class factuurController extends LoggedInPermission{
        protected $factuurModel;
        protected $abonnementModel;

        public function __construct(){
            parent::__construct();
            $this->factuurModel = new Data\FileModel("Factuur");
            $this->abonnementModel = new Data\FileModel("Abonnement");

        }

        public function index(){
            $this->request->redirect("main/factuur/overzicht");
        }

        public function overzicht(){
            $this->setTitle("Overzicht | Citypark");
            $id= $this->input->get('id');
            //check of het id van het abo niet NULL is
            if($id!=NULL){
                //main template initialiseren en het abbonement in kwestie ophalen
                $whereID = new Data\Specifier\Where($this->abonnementModel,[
                    new Data\Specifier\WhereCheck('iduser', '==', $this->user->getPersonID()),
                    new Data\Specifier\WhereCheck('id', '==', $id),
                ]);
                $queryCheck = $this->db->select($this->abonnementModel, NULL, $whereID);
                $overzicht = new Page\Template("/mainAttr/factuur/overzicht");
                //kijken of de query gelukt is.
                if($queryCheck!=NULL) {
                    //dan de facturen ophalen
                    $whereFacuur = new Data\Specifier\Where($this->factuurModel, new Data\Specifier\WhereCheck('idabbo', '==', $id));
                    $queryFactuur = $this->db->select($this->factuurModel, NULL, $whereFacuur);
                    //en de facturen pagina initialeren.
                    //voor elke factuur de gegevens toevoegen aan het scherm.
                    foreach ($queryFactuur AS $row) {
                        //status naar benaming
                        if($row['Factuur-status']==1){
                            $status = "Open";
                        } elseif($row['Factuur-status']==2){
                            $status="Voldaan";
                        } elseif($row['Factuur-status']==3){
                            $status="Geblokkeerd";
                        }
                        $factuurrows = new Page\Template("/mainAttr/factuur/row");
                        $factuurrows->add("nmmr", new Page\Text($row['Factuur-id']));
                        $factuurrows->add("id", new Page\Text($id));
                        $factuurrows->add("bedrag", new Page\Text($row['Factuur-bedrag']));
                        $factuurrows->add("maand", new Page\Text($row['Factuur-maand']));
                        $factuurrows->add("status", new Page\Text($status));
                        $factuurrows->add("vervaldatum", new Page\Text($row['Factuur-datumverval']));
                        $overzicht->add("content", $factuurrows);
                    }




                }else{
                    $overzicht->add("content", new Page\Text("Er zijn nog geen facturen uitgebracht."));
                }
                //main template afmaken.
                $overzicht->add('id', new Page\Text($id));
                $this->template->add("content", $overzicht);

            }else{
                //error als het onjuist benaderd wordt
                $this->app->activateEvent(new StatusChangeEvent(404, "Invalid id"));
            }
        }
        public function bank(){
            //test voor factuur aanmaken.
            $result = new Generator\Factuur();
            $result->makeFactuur($this->input->get('id'));
        }
        public function betaal(){
            //test voor factuur aanmaken.
            $result = new Generator\Factuur();
            $result->betaal(7,2);
        }
        public function pdffactuur(){
            //test voor factuur aanmaken.
            $result = new PDF\PDF();
            $facnum = $this->input->arg(0);
            $result->factuur($this->user->getPersonID(),$facnum );
        }


    }