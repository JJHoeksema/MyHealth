<?php
namespace Beheer\Controller;

use Beheer\BeheerPermission;

use Auth\Account;
use DMF\Events\StatusChangeEvent;
use DMF\Page;
use DMF\Data;

class passController extends BeheerPermission
{
    protected $pasModel;
    protected $userModel;
    protected $nawModel;
    protected $aboModel;
    protected $statusModel;

    protected $errors = "";
    protected $aboid;
    protected $pasid;

    public function __construct()
    {
        parent::__construct();
        $this->pasModel = new Data\FileModel("Pas");
        $this->userModel = new Data\FileModel("User");
        $this->aboModel = new Data\FileModel("Abonnement");
        $this->soortModel = new Data\FileModel("Soort_Pas");
        $this->statusModel = new Data\FileModel("Pas_Status");
        $this->aboid = null;
    }

    public function index()
    {
        $this->setTitle("Pasbeheer | CityPark");

        if ($this->input->get('verzonden')){
            $this->aboid = $this->input->get('aboid');
            $whereAboSelect = new Data\Specifier\Where($this->aboModel, new Data\Specifier\WhereCheck('id', '==', $this->aboid));
            $aboResult = $this->db->select($this->aboModel, null, $whereAboSelect);

            if (count($aboResult) == 1) {
                $aboResult = $aboResult[0];
                $this->request->redirect("beheer/pass/overzicht/?aboid=".$this->input->get('aboid')."&verzonden=true");
            } else {
                $this->errors = "Abonnement-id niet gevonden in de database.";
            }

        }

        $pagina = new Page\Template("beheerAttr/pasbeheer");
        $pagina->add("errors", new Page\Text($this->errors));
        $this->template->add("content", $pagina);

    }

    public function overzicht() {

       // if($this->input->post('verzonden') !=NULL){

            //if (!isset($this->aboid)) {
            //    echo $this->aboid;
            $this->aboid = $this->input->get('aboid');
            $whereAboSelect = new Data\Specifier\Where($this->aboModel, new Data\Specifier\WhereCheck('id', '==', $this->aboid));
        //$whereAboSelect = new Data\Specifier\Where($this->aboModel, new Data\Specifier\WhereCheck('id', '==', '14'));
          //      $this->aboid = $this->input->post('id');
          //  }else {
          //      echo $this->aboid;
          //      $whereAboSelect = new Data\Specifier\Where($this->aboModel, new Data\Specifier\WhereCheck('id', '==', $this->aboid));
          //  }

            $aboResult = $this->db->select($this->aboModel, null, $whereAboSelect);

            if (count($aboResult) == 1) {
                $aboResult = $aboResult[0];
            } else {
                $this->errors = "Abonnement-id niet gevonden in de database.";
                $this->request->redirect("beheer/pass");
            }

            if ($this->input->get('blokkeer') == true && $this->input->get('submit')== true) {

                //blokkeer beide passen
                $whereAbonneeBlokkeer = new Data\Specifier\Where($this->pasModel,  [new Data\Specifier\WhereCheck('idabbo', '==', $aboResult["Abonnement-id"]),
                    new Data\Specifier\WhereCheck('status', '==', '1'),
                    new Data\Specifier\WhereCheck('idsoort', '==', '1'),
                    new Data\Specifier\WhereCheck('set', '==', $this->input->get('set'))]);
                $whereBezoekersBlokkeer = new Data\Specifier\Where($this->pasModel, [new Data\Specifier\WhereCheck('idabbo', '==', $aboResult["Abonnement-id"]),
                    new Data\Specifier\WhereCheck('status', '==', '1'),
                    new Data\Specifier\WhereCheck('idsoort', '==', '2'),
                    new Data\Specifier\WhereCheck('set', '==', $this->input->get('set'))]);
                $data = [
                    'status' => '2'
                ];
                $setResult = $this->db->select($this->pasModel, null, $whereAbonneeBlokkeer);
                if (count($setResult) == 1) {
                    $this->db->update($this->pasModel, $data, $whereAbonneeBlokkeer);
                    $this->db->update($this->pasModel, $data, $whereBezoekersBlokkeer);
                    $form = "Beide passen zijn geblokkeerd.";
                }else {
                    $form = "Set is niet gevonden.";
                }

            }

            if ($this->input->get('deblokkeer') == true && $this->input->get('submit') == true) {
                //blokkeer beide passen
                $whereAbonneeBlokkeer = new Data\Specifier\Where($this->pasModel,  [new Data\Specifier\WhereCheck('idabbo', '==', $aboResult["Abonnement-id"]),
                    new Data\Specifier\WhereCheck('status', '==', '2'),
                    new Data\Specifier\WhereCheck('idsoort', '==', '1'),
                    new Data\Specifier\WhereCheck('set', '==', $this->input->get('set'))]);
                $whereBezoekersBlokkeer = new Data\Specifier\Where($this->pasModel, [new Data\Specifier\WhereCheck('idabbo', '==', $aboResult["Abonnement-id"]),
                    new Data\Specifier\WhereCheck('status', '==', '2'),
                    new Data\Specifier\WhereCheck('idsoort', '==', '2'),
                    new Data\Specifier\WhereCheck('set', '==', $this->input->get('set'))]);
                $data = [
                    'status' => '1'
                ];
                $setResult = $this->db->select($this->pasModel, null, $whereAbonneeBlokkeer);
                if (count($setResult) == 1) {
                    $this->db->update($this->pasModel, $data, $whereAbonneeBlokkeer);
                    $this->db->update($this->pasModel, $data, $whereBezoekersBlokkeer);
                    $form = "Beide passen zijn gedeblokkeerd.";
                }else {
                    $form = "Set is niet gevonden.";
                }
            }

            //select abonneepassen van huidig ingelogd gebruiker
            $whereAbonneePasSelect = new Data\Specifier\Where($this->pasModel,  [new Data\Specifier\WhereCheck('idabbo', '==', $aboResult["Abonnement-id"]),
                new Data\Specifier\WhereCheck('idsoort', '==', '1'),]);
            $whereSortSelect = new Data\Specifier\Sort($this->pasModel, "set");
            //bezoekerspassen
            $whereBezoekersPasSelect = new Data\Specifier\Where($this->pasModel, [new Data\Specifier\WhereCheck('idabbo', '==', $aboResult["Abonnement-id"]),
                new Data\Specifier\WhereCheck('idsoort', '==', '2')]);

            //query results van abonnee- en bezoekerspassen
            $abonneePasResult = $this->db->select($this->pasModel, null, [$whereAbonneePasSelect, $whereSortSelect]);
            //$abonneePasResultSort = new Data\Specifier\Sort($abonneePasResult, "Pas-set");
            $bezoekersPasResult = $this->db->select($this->pasModel, null, [$whereBezoekersPasSelect, $whereSortSelect]);
            //$bezoekersPasResultSort = new Data\Specifier\Sort($bezoekersPasResult, "Pas-set");

            //controleer of gebruiker kan blokkeren voor beide passen
            /*$kanBlokkeren = true;
            foreach ($abonneePasResult as $abonnee) {
                if ($abonnee["Pas-status"] == 1){
                    $kanBlokkeren = true;
                } else {
                    $kanBlokkeren = false;
                }
            }
            foreach ($bezoekersPasResult as $bezoeker) {
                if ($bezoeker["Pas-status"] == 1){
                    $kanBlokkeren = true;
                } else {
                    $kanBlokkeren = false;
                }
            }*/

            //if ($kanBlokkeren) {
                $knopoverzicht = new Page\Template("beheerAttr/pasbeheer-knopblokkeer");
                $knopoverzicht->add("form", new Page\Text($form));
                $knopoverzicht->add("aboid", new Page\Text($this->aboid));
                $this->template->add("content", $knopoverzicht);
            //} else {
                $knopoverzicht = new Page\Template("beheerAttr/pasbeheer-knopdeblokkeer");
                $knopoverzicht->add("form", new Page\Text($form));
                $knopoverzicht->add("aboid", new Page\Text($this->aboid));
                $this->template->add("content", $knopoverzicht);
           // }


            //laat gegevens zien van elke abonneepas van gebruiker
            foreach ($abonneePasResult as $abonnee) {

                $soortPasSelect = new Data\Specifier\Where($this->soortModel, new Data\Specifier\WhereCheck('id', '==', $abonnee["Pas-idsoort"]));
                $soortPas = $this->db->select($this->soortModel, null, $soortPasSelect);
                if (count($soortPas) == 1) {
                    $soortPas = $soortPas[0];
                }else {
                    return;
                }

                $statusPasSelect = new Data\Specifier\Where($this->statusModel, new Data\Specifier\WhereCheck('id', '==', $abonnee["Pas-status"]));
                $statusPas = $this->db->select($this->statusModel, null, $statusPasSelect);
                if (count($statusPas) == 1) {
                    $statusPas = $statusPas[0];
                }else {
                    return;
                }

                $pasoverzicht = new Page\Template("beheerAttr/pasbeheer-overzicht");
                $pasoverzicht->add("pastitel", new Page\Text("Abonneepas"));
                //$pasoverzicht->add("form", new Page\Text($form));
                $pasoverzicht->add("aboid", new Page\Text($abonnee["Pas-idabbo"]));
                $pasoverzicht->add("pasid", new Page\Text($abonnee["Pas-id"]));
                $pasoverzicht->add("idsoort", new Page\Text($soortPas["Soort_Pas-naam"]));
                $pasoverzicht->add("status", new Page\Text($statusPas["Pas_Status-naam"]));
                $pasoverzicht->add("set", new Page\Text($abonnee["Pas-set"]));
                $pasoverzicht->add("reknummer", new Page\Text($abonnee["Pas-reknummer"]));
                $this->template->add("content", $pasoverzicht);
            }

            //laat gegevens zien van elke bezoekerspas van gebruiker
            foreach ($bezoekersPasResult as $bezoeker) {

                $soortPasSelect2 = new Data\Specifier\Where($this->soortModel, new Data\Specifier\WhereCheck('id', '==', $bezoeker["Pas-idsoort"]));
                $soortPas2 = $this->db->select($this->soortModel, null, $soortPasSelect2);
                if (count($soortPas2) == 1) {
                    $soortPas2 = $soortPas2[0];
                }else {
                    return;
                }

                $statusPasSelect2 = new Data\Specifier\Where($this->statusModel, new Data\Specifier\WhereCheck('id', '==', $bezoeker["Pas-status"]));
                $statusPas2 = $this->db->select($this->statusModel, null, $statusPasSelect2);
                if (count($statusPas2) == 1) {
                    $statusPas2 = $statusPas2[0];
                }else {
                    return;
                }

                $pasoverzicht = new Page\Template("beheerAttr/pasbeheer-overzicht");
                $pasoverzicht->add("pastitel", new Page\Text("Bezoekerspas"));
                //$pasoverzicht->add("form", new Page\Text($form));
                $pasoverzicht->add("aboid", new Page\Text($bezoeker["Pas-idabbo"]));
                $pasoverzicht->add("pasid", new Page\Text($bezoeker["Pas-id"]));
                $pasoverzicht->add("idsoort", new Page\Text($soortPas2["Soort_Pas-naam"]));
                $pasoverzicht->add("status", new Page\Text($statusPas2["Pas_Status-naam"]));
                $pasoverzicht->add("set", new Page\Text($bezoeker["Pas-set"]));
                $pasoverzicht->add("reknummer", new Page\Text($bezoeker["Pas-reknummer"]));
                $this->template->add("content", $pasoverzicht);
            }
        }
    //}

    public function blokkeren(){

        /*$whereSelect = new Data\Specifier\Where($this->pasModel, new Data\Specifier\WhereCheck('id', '==', $this->user->getPersonID()));
        $this->user->getUsername();
        $result = $this->db->select($this->pasModel,null, $whereSelect);
        if(count($result) == 1){
            $result = $result[0];
        }else {
            return;
        }*/
        $this->aboid = $this->input->get('aboid');
        $blokkeren = new Page\Template("beheerAttr/pasbeheer-blokkeren");
        $blokkeren->add("aboid", new Page\Text($this->aboid));
        //$blokkeren->add("idabbo", new Page\Text($bezoeker["Pas-idabbo"]));
        $this->template->add("content", $blokkeren);


    }

    public function deblokkeren(){

        /*$whereSelect = new Data\Specifier\Where($this->pasModel, new Data\Specifier\WhereCheck('id', '==', $this->user->getPersonID()));
        $this->user->getUsername();
        $result = $this->db->select($this->pasModel,null, $whereSelect);
        if(count($result) == 1){
            $result = $result[0];
        }else {
            return;
        }*/
        $this->aboid = $this->input->get('aboid');
        $deblokkeren = new Page\Template("beheerAttr/pasbeheer-deblokkeren");
        $deblokkeren->add("aboid", new Page\Text($this->aboid));
        //$blokkeren->add("idabbo", new Page\Text($bezoeker["Pas-idabbo"]));
        //$blokkeren->add("idabbo", new Page\Text($bezoeker["Pas-idabbo"]));
        $this->template->add("content", $deblokkeren);

    }
}