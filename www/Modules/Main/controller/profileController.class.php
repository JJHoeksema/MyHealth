<?php

namespace Main\Controller;


use DMF\Page;
use DMF\Data;
use Main\LoggedInPermission;

class profileController extends LoggedInPermission {
    protected $nawModel;
    protected $userModel;

    public function __construct(){
        parent::__construct();
        if(!$this->user->isLoggedIn()) $this->request->redirect('main/index/register');
        $this->nawModel = new Data\FileModel("Naw");
        $this->userModel = new Data\FileModel("User");
    }


    public function index(){
        $form="";
        $whereSelect = new Data\Specifier\Where($this->nawModel, new Data\Specifier\WhereCheck('id', '==', $this->user->getPersonID()));

        if($this->input->post('verzonden') !=NULL){
            $data = [
                'naam' => $this->input->post('naam'),
                'achternaam' => $this->input->post('achternaam'),
                'land' => $this->input->post('land'),
                'plaats' => $this->input->post('plaats'),
                'straat' => $this->input->post('straat'),
                'huisnummer' => $this->input->post('huisnummer'),
                'toevoeging' => $this->input->post('toevoeging'),
                'telnummer1' => $this->input->post('telefoon1' ,null, false),
                'telnummer2' => $this->input->post('telefoon2' ,null, false),
                'reknummer' => $this->input->post('reknummer'),
                'postcode' => $this->input->post('postcode')

            ];
            $this->db->update($this->nawModel, $data, $whereSelect);
            $form= "De gegevens zijn opgeslagen";
        }



        $this->user->getUsername();
        $result = $this->db->select($this->nawModel,null, $whereSelect);
        if(count($result) == 1){
            $result = $result[0];
        }else {
            return;
        }

        $profieloverzicht = new Page\Template("mainAttr/profiel");
        $profieloverzicht->add("form", new Page\Text($form) );
        $profieloverzicht->add("email", new Page\Text($this->user->getUsername()) );
        $profieloverzicht->add("naam", new Page\Text($result["Naw-naam"]) );
        $profieloverzicht->add("achternaam", new Page\Text($result["Naw-achternaam"]) );
        $profieloverzicht->add("land", new Page\Text($result["Naw-land"]) );
        $profieloverzicht->add("plaats", new Page\Text($result["Naw-plaats"]) );
        $profieloverzicht->add("postcode", new Page\Text($result["Naw-postcode"]) );
        $profieloverzicht->add("straat", new Page\Text($result["Naw-straat"]) );
        $profieloverzicht->add("huisnummer", new Page\Text($result["Naw-huisnummer"]) );
        $profieloverzicht->add("toevoeging", new Page\Text($result["Naw-toevoeging"]) );
        $profieloverzicht->add("telefoon1", new Page\Text($result["Naw-telnummer1"]) );
        $profieloverzicht->add("telefoon2", new Page\Text($result["Naw-telnummer2"]) );
        $profieloverzicht->add("reknummer", new Page\Text($result["Naw-reknummer"]) );
        $this->template->add("content", $profieloverzicht);

    }

    public function bewerken(){
        $whereSelect = new Data\Specifier\Where($this->nawModel, new Data\Specifier\WhereCheck('id', '==', $this->user->getPersonID()));
        $this->user->getUsername();
        $result = $this->db->select($this->nawModel,null, $whereSelect);
        if(count($result) == 1){
            $result = $result[0];
        }else {
            return;
        }
        $bewerken = new Page\Template("mainAttr/profiel-bewerken");
        $bewerken->add("email", new Page\Text($this->user->getUsername()) );
        $bewerken->add("naam", new Page\Text($result["Naw-naam"]) );
        $bewerken->add("achternaam", new Page\Text($result["Naw-achternaam"]) );
        $bewerken->add("land", new Page\Text($result["Naw-land"]) );
        $bewerken->add("plaats", new Page\Text($result["Naw-plaats"]) );
        $bewerken->add("postcode", new Page\Text($result["Naw-postcode"]) );
        $bewerken->add("straat", new Page\Text($result["Naw-straat"]) );
        $bewerken->add("huisnummer", new Page\Text($result["Naw-huisnummer"]) );
        $bewerken->add("toevoeging", new Page\Text($result["Naw-toevoeging"]) );
        $bewerken->add("telefoon1", new Page\Text($result["Naw-telnummer1"]) );
        $bewerken->add("telefoon2", new Page\Text($result["Naw-telnummer2"]) );
        $bewerken->add("reknummer", new Page\Text($result["Naw-reknummer"]) );
        $this->template->add("content", $bewerken);

        //$this->request->redirect("main/profile");


    }
}
