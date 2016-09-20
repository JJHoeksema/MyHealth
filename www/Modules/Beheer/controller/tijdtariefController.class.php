<?php
    namespace Beheer\Controller;

    use Beheer\BeheerPermission;
    use DateTime;
    use DMF\Data;
    use DMF\Page;

    class tijdtariefController extends BeheerPermission{
        protected $tijdTariefModel;

        public function __construct(){
            parent::__construct();
            $this->tijdTariefModel = new Data\FileModel("Tijd_Tarief");
        }
        
        public function index(){
            $this->setTitle("Beheer | Tijd Tarief");

            $tijdtariefTop = new Page\Template("beheerAttr/tijdtarief/tijdtariefTop");
            $this->template->add("content", $tijdtariefTop);
            
            $whereCat = new Data\Specifier\Where($this->tijdTariefModel, new Data\Specifier\WhereCheck('categorie', '==', $this->input->get('categorie')));
            $tijdTariefQuery = $this->db->select($this->tijdTariefModel,null, $whereCat);
            
            foreach($tijdTariefQuery AS $rij) :
                $inhoudTijdTarief = new Page\Template("beheerAttr/tijdtarief/tijdtariefBody");
                $inhoudTijdTarief->add("id", new Page\Text($rij["Tijd_Tarief-id"]) );
                $inhoudTijdTarief->add("categorie", new Page\Text($rij["Tijd_Tarief-categorie"]) );
                //$inhoudTijdTarief->add("dagbegin", new Page\Text($rij["Tijd_Tarief-dagbegin"]) );
                if($rij["Tijd_Tarief-dagbegin"] == 1) :
                    $inhoudTijdTarief->add("dagbegin", new Page\Text("Maandag") );
                elseif($rij["Tijd_Tarief-dagbegin"] == 2) :
                    $inhoudTijdTarief->add("dagbegin", new Page\Text("Dinsdag") );
                elseif($rij["Tijd_Tarief-dagbegin"] == 3) :
                    $inhoudTijdTarief->add("dagbegin", new Page\Text("Woensdag") );
                elseif($rij["Tijd_Tarief-dagbegin"] == 4) :
                    $inhoudTijdTarief->add("dagbegin", new Page\Text("Donderdag") );
                elseif($rij["Tijd_Tarief-dagbegin"] == 5) :
                    $inhoudTijdTarief->add("dagbegin", new Page\Text("Vrijdag") );
                elseif($rij["Tijd_Tarief-dagbegin"] == 6) :
                    $inhoudTijdTarief->add("dagbegin", new Page\Text("Zaterdag") );
                elseif($rij["Tijd_Tarief-dagbegin"] == 7) :
                    $inhoudTijdTarief->add("dagbegin", new Page\Text("Zondag") );
                endif;
                //$inhoudTijdTarief->add("dageind", new Page\Text($rij["Tijd_Tarief-dageind"]) );
                if($rij["Tijd_Tarief-dageind"] == 1) :
                    $inhoudTijdTarief->add("dageind", new Page\Text("Maandag") );
                elseif($rij["Tijd_Tarief-dageind"] == 2) :
                    $inhoudTijdTarief->add("dageind", new Page\Text("Dinsdag") );
                elseif($rij["Tijd_Tarief-dageind"] == 3) :
                    $inhoudTijdTarief->add("dageind", new Page\Text("Woensdag") );
                elseif($rij["Tijd_Tarief-dageind"] == 4) :
                    $inhoudTijdTarief->add("dageind", new Page\Text("Donderdag") );
                elseif($rij["Tijd_Tarief-dageind"] == 5) :
                    $inhoudTijdTarief->add("dageind", new Page\Text("Vrijdag") );
                elseif($rij["Tijd_Tarief-dageind"] == 6) :
                    $inhoudTijdTarief->add("dageind", new Page\Text("Zaterdag") );
                elseif($rij["Tijd_Tarief-dageind"] == 7) :
                    $inhoudTijdTarief->add("dageind", new Page\Text("Zondag") );
                endif;
                $inhoudTijdTarief->add("tijdstart", new Page\Text($rij["Tijd_Tarief-tijdstart"]) );
                $inhoudTijdTarief->add("tijdeind", new Page\Text($rij["Tijd_Tarief-tijdeind"]) );
                $inhoudTijdTarief->add("begindatum", new Page\Text($rij["Tijd_Tarief-begindatum"]) );
                $inhoudTijdTarief->add("einddatum", new Page\Text($rij["Tijd_Tarief-einddatum"]) );
                
                $this->template->add("content", $inhoudTijdTarief);
                
                $tabelKnopTijdtarief = new Page\Template("beheerAttr/tijdtarief/tijdtariefKnop");
                $tabelKnopTijdtarief->add("id", new Page\Text($rij["Tijd_Tarief-id"]) );
                $this->template->add("content", $tabelKnopTijdtarief);

            endforeach;
            
            $tijdtariefBottom = new Page\Template("beheerAttr/tijdtarief/tijdtariefBottom");
            $this->template->add("content", $tijdtariefBottom);
        }
        
        /*public function bewerkRij(){
          $this->setTitle("Beheer | tijdtarief");
            
            $whereId = new Data\Specifier\Where($this->tijdTariefModel, new Data\Specifier\WhereCheck('id', '==', $this->input->get('id')));
            $query = $this->db->select($this->tijdTariefModel,null, $whereId);
            
            $tijdtariefBewerkTop = new Page\Template("beheerAttr/tijdtarief/tijdtariefTopBewerken");
            $this->template->add("content", $tijdtariefBewerkTop);
               
            foreach($query AS $rij) :
                $inhoudBewerkTijdTarief = new Page\Template("beheerAttr/tijdtarief/tijdtariefBodyBewerken");;
                $inhoudBewerkTijdTarief->add("id", new Page\Text($rij["Tijd_Tarief-id"]) );
                $inhoudBewerkTijdTarief->add("categorie", new Page\Text($rij["Tijd_Tarief-categorie"]) );
                $inhoudBewerkTijdTarief->add("dagbegin", new Page\Text($rij["Tijd_Tarief-dagbegin"]) );
                $inhoudBewerkTijdTarief->add("dageind", new Page\Text($rij["Tijd_Tarief-dageind"]) );
                $inhoudBewerkTijdTarief->add("tijdstart", new Page\Text($rij["Tijd_Tarief-tijdstart"]) );
                $inhoudBewerkTijdTarief->add("tijdeind", new Page\Text($rij["Tijd_Tarief-tijdeind"]) );
                $inhoudBewerkTijdTarief->add("begindatum", new Page\Text($rij["Tijd_Tarief-begindatum"]) );
                $inhoudBewerkTijdTarief->add("einddatum", new Page\Text($rij["Tijd_Tarief-einddatum"]) );
                
                $this->template->add("content", $inhoudBewerkTijdTarief);
            endforeach;
            
            $tijdtariefBewerkBottom = new Page\Template("beheerAttr/tijdtarief/tijdtariefBottomBewerken");
            $this->template->add("content", $tijdtariefBewerkBottom);
            
            if($this->input->post('opslaan') != null) :
                
                $epoch = time()+60*60;
                $nu = new DateTime("@$epoch");

                // begin datum setten naar eerste van de maand
                $begindatum = explode("-", $this->input->post('begindatum'));      
                $begindatum[2] = 1;
                $nieuwebegindatum = implode("-", $begindatum);

                //eind datum
                $einddatum = explode("-", $this->input->post('einddatum'));
                $einddatum[2] = 1;
                $einddatum[1] += 1;

                $laatstedag = mktime(0, 0, 0, $einddatum[1], $einddatum[2]-1, $einddatum[0]);

                $nieuweeinddatum = new DateTime();
                $nieuweeinddatum->setTimestamp($laatstedag);
                
                if($nieuwebegindatum > $nu->format('Y-m-j') && $nieuwebegindatum != $row["Tijd_Tarief-begindatum"]) :
                    
                    //Er is een null aanwezig
                    tijdtariefController::nullCheck($nieuwebegindatum);
                    
                    //Controlle op overlap van data
                    tijdtariefController::datumOverlap($nieuwebegindatum, $row["Tijd_Tarief-categorie"]);
                    
                    if($nieuweeinddatum->format('Y-m-d') == "1970-01-01"):
                        $data = [
                            'dagbegin' => $this->input->post('dagbegin'),
                            'dageind' => $this->input->post('dageind'),
                            'tijdstart' => $this->input->post('tijdstart'),
                            'tijdeind' => $this->input->post('tijdeind'),
                            'begindatum' => $nieuwebegindatum,
                            'createdate' => $nu->format('Y-m-d H:i:s'),
                            'createuser' => $this->user->getPersonID()
                        ];
                        $this->db->insert($this->tijdTariefModel, $data);
                        $this->request->redirect("Beheer/tijdtarief/");
                    else:
                        if($nieuweeinddatum->format('Y-m-d')>=$nieuwebegindatum) :
                            $data = [
                                'dagbegin' => $this->input->post('dagbegin'),
                                'dageind' => $this->input->post('dageind'),
                                'tijdstart' => $this->input->post('tijdstart'),
                                'tijdeind' => $this->input->post('tijdeind'),
                                'begindatum' => $nieuwebegindatum,
                                'einddatum' => $nieuweeinddatum->format('Y-m-d'),
                                'createdate' => $nu->format('Y-m-d H:i:s'),
                                'createuser' => $this->user->getPersonID()
                            ];
                            $this->db->insert($this->tijdTariefModel, $data);
                            $this->request->redirect("Beheer/tijdtarief/");
                        else:
                            $error = new Page\Template("beheerAttr/tijdtarief/tijdtariefErrorBewerken");
                            $error->add("error", new Page\Text("Einddatum kleiner dan startdatum"));
                            $this->template->add("content", $error);
                        endif;
                    endif;
                else:
                    $error = new Page\Template("beheerAttr/tijdtarief/tijdtariefErrorBewerken");
                    $error->add("error", new Page\Text("Begindatum kleiner dan huidige datum of gelijk aan startdatum"));
                    $this->template->add("content", $error);
                endif;
            endif;
        }
        
        public function nullCheck($nieuwebegindatum){
            $where = new Data\Specifier\Where($this->tijdTariefModel, [  new Data\Specifier\WhereCheck('einddatum', '==', null),
                                                                     new Data\Specifier\WhereCheck('id', '==', $this->input->get('id'))]);
            $query=$this->db->select($this->tijdTariefModel,null, $where);
            
            $aantal = count($query);
            
            if($aantal>0) :
                $whereID = new Data\Specifier\Where($this->tijdTariefModel, new Data\Specifier\WhereCheck('id', '==',$query[0]["Tijd_Tarief-id"]));

                $einddatum = explode("-", $nieuwebegindatum);
                $temp = mktime(0, 0, 0, $einddatum[1], $einddatum[2]-1, $einddatum[0]);

                $vorigedag = new DateTime();
                $vorigedag->setTimestamp($temp);

                $data = [
                    'einddatum' => $vorigedag->format('Y-m-d'),
                    ];

                $this->db->update($this->tijdTariefModel,$data,$whereID);
            endif;
        }
        
        public function datumOverlap($nieuwebegindatum, $categorie){
            $where = new Data\Specifier\Where($this->tijdTariefModel, [  new Data\Specifier\WhereCheck('categorie', '==', $categorie)]);
            $sortDate = new Data\Specifier\Sort($this->tijdTariefModel, 'einddatum', false);
            $query=$this->db->select($this->tijdTariefModel,null, [$where, $sortDate]);
            
            if($query[0]["Tijd_Tarief-datumeind"]>$nieuwebegindatum) :
                $whereID = new Data\Specifier\Where($this->tijdTariefModel, new Data\Specifier\WhereCheck('id', '==',$query[0]["Tijd_Tarief-id"]));

                $einddatum = explode("-", $nieuwebegindatum);
                $temp = mktime(0, 0, 0, $einddatum[1], $einddatum[2]-1, $einddatum[0]);

                $vorigedag = new DateTime();
                $vorigedag->setTimestamp($temp);

                $data_update = [
                    'einddatum' => $vorigedag->format('Y-m-d'),
                    ];

                $this->db->update($this->tijdTariefModel,$data_update,$whereID);
            endif;
        }*/
    }

