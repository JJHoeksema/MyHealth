<?php
    namespace Beheer\Controller;

    use Beheer\BeheerPermission;
    use DateTime;
    use DMF\Data;
    use DMF\Page;

    class tariefController extends BeheerPermission{
        protected $tariefModel;

        public function __construct(){
            parent::__construct();
            $this->tariefModel = new Data\FileModel("Tarief_cat");
        }
        
        public function index(){
            $this->setTitle("Beheer | Tarief");

            $overzichtTariefCat = new Page\Template("beheerAttr/tarief/tarief");
            $this->template->add("content", $overzichtTariefCat);
            
            $tariefCatQuery = $this->db->select($this->tariefModel,null, null);
            
            $where1 = new Data\Specifier\Where($this->tariefModel, [  new Data\Specifier\WhereCheck('categorie', '==', 1)]);
            $sortDate1 = new Data\Specifier\Sort($this->tariefModel, 'createdate', false);
            $query1=$this->db->select($this->tariefModel,null, [$where1, $sortDate1]);

            $where2 = new Data\Specifier\Where($this->tariefModel, [  new Data\Specifier\WhereCheck('categorie', '==', 2)]);
            $sortDate2 = new Data\Specifier\Sort($this->tariefModel, 'createdate', false);
            $query2=$this->db->select($this->tariefModel,null, [$where2, $sortDate2]);

            $where3 = new Data\Specifier\Where($this->tariefModel, [  new Data\Specifier\WhereCheck('categorie', '==', 3)]);
            $sortDate3 = new Data\Specifier\Sort($this->tariefModel, 'createdate', false);
            $query3=$this->db->select($this->tariefModel,null, [$where3, $sortDate3]);
            
            $id1 = $query1[0]["Tarief_cat-id"];
            $id2 = $query2[0]["Tarief_cat-id"];
            $id3 = $query3[0]["Tarief_cat-id"];
            
            foreach($tariefCatQuery AS $row) :
                $overzichtInhoudTarief = new Page\Template("beheerAttr/tarief/tabelInhoudTarief");
                $overzichtInhoudTarief->add("id", new Page\Text($row["Tarief_cat-id"]) );
                $overzichtInhoudTarief->add("categorie", new Page\Text($row["Tarief_cat-categorie"]) );
                $overzichtInhoudTarief->add("datumbegin", new Page\Text($row["Tarief_cat-datumbegin"]) );
                $overzichtInhoudTarief->add("datumeind", new Page\Text($row["Tarief_cat-datumeind"]) );
                $overzichtInhoudTarief->add("tarief", new Page\Text($row["Tarief_cat-tarief"]) );
                $overzichtInhoudTarief->add("permin", new Page\Text($row["Tarief_cat-permin"]) );
                $overzichtInhoudTarief->add("max", new Page\Text($row["Tarief_cat-max"]) );
                $overzichtInhoudTarief->add("createdate", new Page\Text($row["Tarief_cat-createdate"]) );
                
                $this->template->add("content", $overzichtInhoudTarief);
                
                if($row["Tarief_cat-id"] == $id1) :
                    $tabelKnopTarief = new Page\Template("beheerAttr/tarief/tabelKnopTarief");
                    $tabelKnopTarief->add("id", new Page\Text($row["Tarief_cat-id"]) );
                    $tabelKnopTarief->add("categorie", new Page\Text($row["Tarief_cat-categorie"]) );
                    $this->template->add("content", $tabelKnopTarief);
                elseif($row["Tarief_cat-id"] == $id2) :
                    $tabelKnopTarief = new Page\Template("beheerAttr/tarief/tabelKnopTarief");
                    $tabelKnopTarief->add("id", new Page\Text($row["Tarief_cat-id"]) );
                    $tabelKnopTarief->add("categorie", new Page\Text($row["Tarief_cat-categorie"]) );
                    $this->template->add("content", $tabelKnopTarief);
                elseif($row["Tarief_cat-id"] == $id3) :
                    $tabelKnopTarief = new Page\Template("beheerAttr/tarief/tabelKnopTarief");
                    $tabelKnopTarief->add("id", new Page\Text($row["Tarief_cat-id"]) );
                    $tabelKnopTarief->add("categorie", new Page\Text($row["Tarief_cat-categorie"]) );
                    $this->template->add("content", $tabelKnopTarief);
                endif;
            endforeach;
            
            $overzichtInhoudTariefEind = new Page\Template("beheerAttr/tarief/tariefInhoudTariefEind");
            $this->template->add("content", $overzichtInhoudTariefEind);
        }
        
        public function bewerkcat(){
            $this->setTitle("Beheer | Tarief");
            
            $where1 = new Data\Specifier\Where($this->tariefModel, [  new Data\Specifier\WhereCheck('categorie', '==', 1)]);
            $sortDate1 = new Data\Specifier\Sort($this->tariefModel, 'createdate', false);
            $query1=$this->db->select($this->tariefModel,null, [$where1, $sortDate1]);

            $where2 = new Data\Specifier\Where($this->tariefModel, [  new Data\Specifier\WhereCheck('categorie', '==', 2)]);
            $sortDate2 = new Data\Specifier\Sort($this->tariefModel, 'createdate', false);
            $query2=$this->db->select($this->tariefModel,null, [$where2, $sortDate2]);

            $where3 = new Data\Specifier\Where($this->tariefModel, [  new Data\Specifier\WhereCheck('categorie', '==', 3)]);
            $sortDate3 = new Data\Specifier\Sort($this->tariefModel, 'createdate', false);
            $query3=$this->db->select($this->tariefModel,null, [$where3, $sortDate3]);
            
            $id1 = $query1[0]["Tarief_cat-id"];
            $id2 = $query2[0]["Tarief_cat-id"];
            $id3 = $query3[0]["Tarief_cat-id"];
            
            if($this->input->get('id') == $id1 || $this->input->get('id') == $id2 || $this->input->get('id') == $id3) :
            
                $whereCatId = new Data\Specifier\Where($this->tariefModel, new Data\Specifier\WhereCheck('id', '==', $this->input->get('id')));
                $query = $this->db->select($this->tariefModel,null, $whereCatId);

                $categorieBewerkTop = new Page\Template("beheerAttr/tarief/categorieBewerkTop");
                $this->template->add("content", $categorieBewerkTop);

                foreach($query AS $row) :
                    $categorieBewerk = new Page\Template("beheerAttr/tarief/categorieBewerk");
                    $categorieBewerk->add("categorie", new Page\Text($row["Tarief_cat-categorie"]) );
                    $categorieBewerk->add("datumbegin", new Page\Text($row["Tarief_cat-datumbegin"]) );
                    $categorieBewerk->add("datumeind", new Page\Text($row["Tarief_cat-datumeind"]) );
                    $categorieBewerk->add("tarief", new Page\Text($row["Tarief_cat-tarief"]) );
                    $categorieBewerk->add("permin", new Page\Text($row["Tarief_cat-permin"]) );
                    $categorieBewerk->add("max", new Page\Text($row["Tarief_cat-max"]) );
                    $this->template->add("content", $categorieBewerk);
                endforeach;

                $categorieBewerkBottom = new Page\Template("beheerAttr/tarief/categorieBewerkBottom");
                $this->template->add("content", $categorieBewerkBottom);

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
                        tariefController::nullCheck($nieuwebegindatum);

                        //Controlle op overlap van data
                        tariefController::datumOverlap($nieuwebegindatum, $row["Tarief_cat-categorie"]);

                        if($nieuweeinddatum->format('Y-m-d') == "1970-01-01"):
                            $data = [
                                'categorie' => $row["Tarief_cat-categorie"],
                                'datumbegin' => $nieuwebegindatum,
                                'tarief' => $this->input->post('tarief'),
                                'permin' => $row["Tarief_cat-permin"],
                                'max' => $this->input->post('max'),
                                'createdate' => $nu->format('Y-m-d H:i:s'),
                                'createuser' => $this->user->getPersonID()
                            ];
                            $this->db->insert($this->tariefModel, $data);
                            $this->request->redirect("Beheer/tarief/");
                        else:
                            if($nieuweeinddatum->format('Y-m-d')>=$nieuwebegindatum) :
                                $data = [
                                    'categorie' => $row["Tarief_cat-categorie"],
                                    'datumbegin' => $nieuwebegindatum,
                                    'datumeind' => $nieuweeinddatum->format('Y-m-d'),
                                    'tarief' => $this->input->post('tarief'),
                                    'permin' => $row["Tarief_cat-permin"],
                                    'max' => $this->input->post('max'),
                                    'createdate' => $nu->format('Y-m-d H:i:s'),
                                    'createuser' => $this->user->getPersonID()
                                ];
                                $this->db->insert($this->tariefModel, $data);
                                $this->request->redirect("Beheer/tarief/");
                            else:
                                $error = new Page\Template("beheerAttr/tarief/categorieBewerkError");
                                $error->add("error", new Page\Text("Einddatum kleiner dan startdatum"));
                                $this->template->add("content", $error);
                            endif;
                        endif;
                    else:
                        $error = new Page\Template("beheerAttr/tarief/categorieBewerkError");
                        $error->add("error", new Page\Text("Begindatum kleiner dan huidige datum of gelijk aan startdatum"));
                        $this->template->add("content", $error);
                    endif;
                endif;
            else:
                $error = new Page\Template("beheerAttr/tarief/categorieBewerkError");
                $error->add("error", new Page\Text("Dit record kun je niet bewerken"));
                $this->template->add("content", $error);
            endif;
        }
        
        public function nullCheck($nieuwebegindatum){
            $where = new Data\Specifier\Where($this->tariefModel, [  new Data\Specifier\WhereCheck('datumeind', '==', null),
                                                                     new Data\Specifier\WhereCheck('id', '==', $this->input->get('id'))]);
            $query=$this->db->select($this->tariefModel,null, $where);
            
            $aantal = count($query);
            
            if($aantal>0) :
                $whereID = new Data\Specifier\Where($this->tariefModel, new Data\Specifier\WhereCheck('id', '==',$query[0]["Tarief_cat-id"]));

                $einddatum = explode("-", $nieuwebegindatum);
                $temp = mktime(0, 0, 0, $einddatum[1], $einddatum[2]-1, $einddatum[0]);

                $vorigedag = new DateTime();
                $vorigedag->setTimestamp($temp);

                $data = [
                    'datumeind' => $vorigedag->format('Y-m-d'),
                    ];

                $this->db->update($this->tariefModel,$data,$whereID);
            endif;
        }
        
        public function datumOverlap($nieuwebegindatum, $categorie){
            $where = new Data\Specifier\Where($this->tariefModel, [  new Data\Specifier\WhereCheck('categorie', '==', $categorie)]);
            $sortDate = new Data\Specifier\Sort($this->tariefModel, 'datumeind', false);
            $query=$this->db->select($this->tariefModel,null, [$where, $sortDate]);
            
            if($query[0]["Tarief_cat-datumeind"]>$nieuwebegindatum) :
                $whereID = new Data\Specifier\Where($this->tariefModel, new Data\Specifier\WhereCheck('id', '==',$query[0]["Tarief_cat-id"]));

                $einddatum = explode("-", $nieuwebegindatum);
                $temp = mktime(0, 0, 0, $einddatum[1], $einddatum[2]-1, $einddatum[0]);

                $vorigedag = new DateTime();
                $vorigedag->setTimestamp($temp);

                $data_update = [
                    'datumeind' => $vorigedag->format('Y-m-d'),
                    ];

                $this->db->update($this->tariefModel,$data_update,$whereID);
            endif;
        }
    }