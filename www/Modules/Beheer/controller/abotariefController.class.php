<?php
    namespace Beheer\Controller;

    use Beheer\BeheerPermission;
    use DateTime;
    use DMF\Data;
    use DMF\Page;

    class abotariefController extends BeheerPermission{
        protected $abotariefModel;

        public function __construct(){
            parent::__construct();
            $this->abotariefModel = new Data\FileModel("Abo_Tarief");
        }
        
        public function index(){
            $this->setTitle("Beheer | Abonnement Tarief");

            $overzichtTariefCat = new Page\Template("beheerAttr/abotarief/abotariefTop");
            $this->template->add("content", $overzichtTariefCat);
            
            $sortDate = new Data\Specifier\Sort($this->abotariefModel, 'createdate', false);
            $abotariefQuery = $this->db->select($this->abotariefModel,null, $sortDate);
           
            $id = $abotariefQuery[0]["Abo_Tarief-id"];
            
            foreach($abotariefQuery AS $row) :
                $overzichtInhoudAboTarief = new Page\Template("beheerAttr/abotarief/abotariefBody");
                $overzichtInhoudAboTarief->add("id", new Page\Text($row["Abo_Tarief-id"]) );
                $overzichtInhoudAboTarief->add("datumbegin", new Page\Text($row["Abo_Tarief-datumbegin"]) );
                $overzichtInhoudAboTarief->add("datumeind", new Page\Text($row["Abo_Tarief-datumeind"]) );
                $overzichtInhoudAboTarief->add("tarief", new Page\Text($row["Abo_Tarief-tarief"]) );
                $overzichtInhoudAboTarief->add("createdate", new Page\Text($row["Abo_Tarief-createdate"]) );
                
                $this->template->add("content", $overzichtInhoudAboTarief);
                
                if($row["Abo_Tarief-id"] == $id) :
                    $tabelKnopAboTarief = new Page\Template("beheerAttr/abotarief/abotariefKnop");
                    $tabelKnopAboTarief->add("id", new Page\Text($row["Abo_Tarief-id"]) );
                    $this->template->add("content", $tabelKnopAboTarief);
                endif;
            endforeach;
            
            $overzichtInhoudAboTariefEind = new Page\Template("beheerAttr/abotarief/abotariefBottom");
            $this->template->add("content", $overzichtInhoudAboTariefEind);
        }
        
        public function bewerkAboTarief(){
            $sortDate = new Data\Specifier\Sort($this->abotariefModel, 'createdate', false);
            $idcheck = $this->db->select($this->abotariefModel,null, $sortDate);
           
            $id = $idcheck[0]["Abo_Tarief-id"];
            
            if($id == $this->input->get('id')) :
                $this->setTitle("Beheer | Bewerk abonnement tarief");

                $whereId = new Data\Specifier\Where($this->abotariefModel, new Data\Specifier\WhereCheck('id', '==', $this->input->get('id')));
                $bewerkQuery = $this->db->select($this->abotariefModel,null, $whereId);

                $categorieBewerkTop = new Page\Template("beheerAttr/abotarief/abotariefBewerkenTop");
                $this->template->add("content", $categorieBewerkTop);

                foreach($bewerkQuery AS $row) :
                    $bewerkAboTarief = new Page\Template("beheerAttr/abotarief/abotariefBewerkenBody");
                    $bewerkAboTarief->add("id", new Page\Text($row["Abo_Tarief-id"]) );
                    $bewerkAboTarief->add("datumbegin", new Page\Text($row["Abo_Tarief-datumbegin"]) );
                    $bewerkAboTarief->add("datumeind", new Page\Text($row["Abo_Tarief-datumeind"]) );
                    $bewerkAboTarief->add("tarief", new Page\Text($row["Abo_Tarief-tarief"]) );
                    $this->template->add("content", $bewerkAboTarief);
                endforeach;

                $bewerkBottom = new Page\Template("beheerAttr/abotarief/abotariefBewerkenBottom");
                $this->template->add("content", $bewerkBottom);

                if($this->input->post('opslaan') != null) :
                    $epoch = time()+60*60;
                    $nu = new DateTime("@$epoch");

                    // begin datum setten naar eerste van de maand
                    $begindatum = explode("-", $this->input->post('datumbegin'));      
                    $begindatum[2] = 1;
                    $nieuwebegindatum = implode("-", $begindatum);

                    //eind datum
                    $einddatum = explode("-", $this->input->post('datumeind'));
                    $einddatum[2] = 1;
                    $einddatum[1] += 1;

                    $laatstedag = mktime(0, 0, 0, $einddatum[1], $einddatum[2]-1, $einddatum[0]);

                    $nieuweeinddatum = new DateTime();
                    $nieuweeinddatum->setTimestamp($laatstedag);

                    if($nieuwebegindatum > $nu->format('Y-m-j') && $nieuwebegindatum != $row["Tarief_cat-datumbegin"]) :

                        //Er is een null aanwezig
                        abotariefController::nullCheck($nieuwebegindatum);

                        //Controlle op overlap van data
                        abotariefController::datumOverlap($nieuwebegindatum);

                        if($nieuweeinddatum->format('Y-m-d') == "1970-01-01"):
                            $data = [
                                'datumbegin' => $nieuwebegindatum,
                                'tarief' => $this->input->post('tarief'),
                                'createdate' => $nu->format('Y-m-d H:i:s'),
                                'createuser' => $this->user->getPersonID()
                            ];
                            $this->db->insert($this->abotariefModel, $data);
                            $this->request->redirect("Beheer/abotarief/");
                        else:
                            if($nieuweeinddatum->format('Y-m-d')>=$nieuwebegindatum) :
                                $data = [
                                    'datumbegin' => $nieuwebegindatum,
                                    'datumeind' => $nieuweeinddatum->format('Y-m-d'),
                                    'tarief' => $this->input->post('tarief'),
                                    'createdate' => $nu->format('Y-m-d H:i:s'),
                                    'createuser' => $this->user->getPersonID()
                                ];
                                $this->db->insert($this->abotariefModel, $data);
                                $this->request->redirect("beheer/abotarief/");
                            else:
                                $error = new Page\Template("beheerAttr/abotarief/abotariefBewerkenError");
                                $error->add("error", new Page\Text("Einddatum kleiner dan startdatum"));
                                $this->template->add("content", $error);
                            endif;
                        endif;
                    else:
                        $error = new Page\Template("beheerAttr/abotarief/abotariefBewerkenError");
                        $error->add("error", new Page\Text("Begindatum kleiner dan huidige datum"));
                        $this->template->add("content", $error);
                    endif;
                endif;
            else:
                $error = new Page\Template("beheerAttr/abotarief/abotariefBewerkenError");
                $error->add("error", new Page\Text("Dit record kun je niet bewerken"));
                $this->template->add("content", $error);
            endif;
        }
        
        public function nullCheck($nieuwebegindatum){
            $where = new Data\Specifier\Where($this->abotariefModel, [  new Data\Specifier\WhereCheck('datumeind', '==', null),
                                                                     new Data\Specifier\WhereCheck('id', '==', $this->input->get('id'))]);
            $query=$this->db->select($this->abotariefModel,null, $where);
            
            $aantal = count($query);
            
            if($aantal>0) :
                $whereID = new Data\Specifier\Where($this->abotariefModel, new Data\Specifier\WhereCheck('id', '==',$query[0]["Abo_Tarief-id"]));

                $einddatum = explode("-", $nieuwebegindatum);
                $temp = mktime(0, 0, 0, $einddatum[1], $einddatum[2]-1, $einddatum[0]);

                $vorigedag = new DateTime();
                $vorigedag->setTimestamp($temp);

                $data = [
                    'datumeind' => $vorigedag->format('Y-m-d'),
                    ];

                $this->db->update($this->abotariefModel,$data,$whereID);
            endif;
        }
        
        public function datumOverlap($nieuwebegindatum){
            $sortDate = new Data\Specifier\Sort($this->abotariefModel, 'datumeind', false);
            $query=$this->db->select($this->abotariefModel,null, $sortDate);
            
            if($query[0]["Abo_Tarief-datumeind"]>$nieuwebegindatum) :
                $whereID = new Data\Specifier\Where($this->abotariefModel, new Data\Specifier\WhereCheck('id', '==',$query[0]["Abo_Tarief-id"]));

                $einddatum = explode("-", $nieuwebegindatum);
                $temp = mktime(0, 0, 0, $einddatum[1], $einddatum[2]-1, $einddatum[0]);

                $vorigedag = new DateTime();
                $vorigedag->setTimestamp($temp);

                $data_update = [
                    'datumeind' => $vorigedag->format('Y-m-d'),
                    ];

                $this->db->update($this->abotariefModel,$data_update,$whereID);
            endif;
        }
    }