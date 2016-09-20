<?php
namespace Main\Controller;

use Main\LoggedInPermission;
use DMF\Page;
use DMF\Data;

class pasController extends LoggedInPermission
{
    protected $pasModel;
    protected $userModel;
    protected $nawModel;
    protected $aboModel;
    protected $statusModel;
    protected $errors;
    protected $aboid;

    public function __construct()
    {
        parent::__construct();
        $this->pasModel = new Data\FileModel("Pas");
        $this->userModel = new Data\FileModel("User");
        $this->aboModel = new Data\FileModel("Abonnement");
        $this->soortModel = new Data\FileModel("Soort_Pas");
        $this->statusModel = new Data\FileModel("Pas_Status");
    }

    public function index()
    {
        $this->setTitle("Pasbeheer | CityPark");

        $this->errors = "";
        //$whereUserSelect = new Data\Specifier\Where($this->aboModel, new Data\Specifier\WhereCheck('iduser', '==', $this->user->getPersonID()));
       // $userResult = $this->db->select($this->aboModel, null, $whereUserSelect);

        /*if (count($userResult) == 1) {
            $userResult = $userResult[0];
        } else {
            return;
        }*/

        if ($this->input->get('verzonden')) {
            $this->aboid = $this->input->get('aboid');
            $whereAboSelect = new Data\Specifier\Where($this->aboModel, [new Data\Specifier\WhereCheck('id', '==', $this->aboid),
                new Data\Specifier\WhereCheck('iduser', '==', $this->user->getPersonID())]);
            $aboResult = $this->db->select($this->aboModel, null, $whereAboSelect);


            if (count($aboResult) == 1 && $aboResult[0]['Abonnement-status'] == 1) {
                $this->request->redirect("main/pas/overzicht/?aboid=" . $this->input->get('aboid') . "&verzonden=true");
            }else {
                $this->errors = "Ongeldig Abonnement-id.";
            }
        }
        $whereUserSelect = new Data\Specifier\Where($this->aboModel, new Data\Specifier\WhereCheck('iduser', '==', $this->user->getPersonID()));
        $userResult = $this->db->select($this->aboModel, null, $whereUserSelect);

        $options = [];
        /*foreach($this->db->select($this->aboModel) as $result){
            $options[$result["Abonnement-id"]] = $result["Abonnement-id"];
        }*/
        foreach($userResult as $result) {
            $options[$result["Abonnement-id"]] = $result["Abonnement-id"];
        }
        $pagina = new Page\Template("mainAttr/pasbeheer");
        $select = new Page\Select($options , $options[0]);
        $select->addAttribute("name", "aboid");
        $pagina->add("abonnementen", $select);
        $pagina->add("errors", new Page\Text($this->errors));
        $this->template->add("content", $pagina);
    }


    public function overzicht(){

    $whereUserSelect = new Data\Specifier\Where($this->aboModel, new Data\Specifier\WhereCheck('iduser', '==', $this->user->getPersonID()));
    $userResult = $this->db->select($this->aboModel, null, $whereUserSelect);
    $whereAboSelect = new Data\Specifier\Where($this->aboModel, [new Data\Specifier\WhereCheck('id', '==', $this->input->get('aboid')),
        new Data\Specifier\WhereCheck('iduser', '==', $this->user->getPersonID())]);
    $aboResult = $this->db->select($this->aboModel, null, $whereAboSelect);

        if (count($aboResult) == 1) {
            $aboResult = $aboResult[0];
        } else {
            return;
        }

        $this->aboid = $this->input->get('aboid');

        if ($this->input->get('blokkeer') != NULL && $this->input->get('submit')) {


            //blokkeer beide passen
            $whereAbonneeBlokkeer = new Data\Specifier\Where($this->pasModel,  [new Data\Specifier\WhereCheck('idabbo', '==', $aboResult["Abonnement-id"]),
                new Data\Specifier\WhereCheck('status', '==', '1'),
                new Data\Specifier\WhereCheck('idsoort', '==', '1')]);
            $whereBezoekersBlokkeer = new Data\Specifier\Where($this->pasModel, [new Data\Specifier\WhereCheck('idabbo', '==', $aboResult["Abonnement-id"]),
                new Data\Specifier\WhereCheck('status', '==', '1'),
                new Data\Specifier\WhereCheck('idsoort', '==', '2')]);
            $data = [
                'status' => '2'
            ];
            $this->db->update($this->pasModel, $data, $whereAbonneeBlokkeer);
            $this->db->update($this->pasModel, $data, $whereBezoekersBlokkeer);
            $this->request->redirect("main/pas/overzicht/?submit=false&aboid=" . $aboResult["Abonnement-id"]);
            $form = "Uw beide passen zijn geblokkeerd.";

        }

        if ($this->input->get('aanvragen') != NULL && $this->input->get('submit')) {
            echo "hoi";

            //wijs 2 passen toe aan huidige persoon
            $pasSelect = new Data\Specifier\Where($this->pasModel, [
                new Data\Specifier\WhereCheck('idsoort', '==', 1),
                new Data\Specifier\WhereCheck('idabbo', '==', NULL)]);
            $bezoekpasSelect = new Data\Specifier\Where($this->pasModel, [
                new Data\Specifier\WhereCheck('idsoort', '==', 2),
                new Data\Specifier\WhereCheck('idabbo', '==', NULL)]);
            $pasQuery = $this->db->select($this->pasModel, NULL, $pasSelect);
            $pasSelect2 = new Data\Specifier\Where($this->pasModel, [new Data\Specifier\WhereCheck('id', '==', $pasQuery[0]['Pas-id'])]);
            $bezoekpasQuery = $this->db->select($this->pasModel, NULL, $bezoekpasSelect);
            $bezoekpasSelect2 = new Data\Specifier\Where($this->pasModel, [new Data\Specifier\WhereCheck('id', '==', $bezoekpasQuery[0]['Pas-id'])]);

            $set = $this->input->get("set") + 1;

            $dataPas = [
                'idabbo' => $aboResult["Abonnement-id"],
                'status' => '1',
                'set' => $set
            ];
            $this->db->update($this->pasModel, $dataPas, $bezoekpasSelect2);
            $this->db->update($this->pasModel, $dataPas, $pasSelect2);

            $this->request->redirect("main/pas/overzicht/?submit=false&aboid=" . $aboResult["Abonnement-id"]);
            $form = "Uw passen zijn aangevraagd.";

        }

        //select abonneepassen van huidig ingelogd gebruiker
        $whereAbonneePasSelect = new Data\Specifier\Where($this->pasModel,  [new Data\Specifier\WhereCheck('idabbo', '==', $aboResult["Abonnement-id"]),
            new Data\Specifier\WhereCheck('idsoort', '==', '1')]);
        //bezoekerspassen
        $whereBezoekersPasSelect = new Data\Specifier\Where($this->pasModel, [new Data\Specifier\WhereCheck('idabbo', '==', $aboResult["Abonnement-id"]),
            new Data\Specifier\WhereCheck('idsoort', '==', '2')]);

        $whereSortSelect = new Data\Specifier\Sort($this->pasModel, "set");

        //query results van abonnee- en bezoekerspassen
        $abonneePasResult = $this->db->select($this->pasModel, null, [$whereAbonneePasSelect, $whereSortSelect]);
        $bezoekersPasResult = $this->db->select($this->pasModel, null, [$whereBezoekersPasSelect, $whereSortSelect]);

        //controleer of gebruiker kan blokkeren voor beide passen
        $kanBlokkeren = false;
        foreach ($abonneePasResult as $abonnee) {
            $hoogsteSet = $abonnee["Pas-set"];
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
        }

        if ($kanBlokkeren) {
            $pasoverzicht = new Page\Template("mainAttr/pasbeheer-knopblokkeer");
            $pasoverzicht->add("aboid", new Page\Text($this->aboid));
            $pasoverzicht->add("set", new Page\Text($hoogsteSet));
            $pasoverzicht->add("form", new Page\Text($form));
            $this->template->add("content", $pasoverzicht);
        } else {
            $pasoverzicht = new Page\Template("mainAttr/pasbeheer-knopaanvraag");
            $pasoverzicht->add("aboid", new Page\Text($this->aboid));
            $pasoverzicht->add("set", new Page\Text($hoogsteSet));
            $pasoverzicht->add("form", new Page\Text($form));
            $this->template->add("content", $pasoverzicht);
        }


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

            $pasoverzicht = new Page\Template("mainAttr/pasbeheer-overzicht");
            $pasoverzicht->add("pastitel", new Page\Text("Abonneepas"));
            //$pasoverzicht->add("form", new Page\Text($form));
            $pasoverzicht->add("idabbo", new Page\Text($abonnee["Pas-idabbo"]));
            $pasoverzicht->add("idsoort", new Page\Text($soortPas["Soort_Pas-naam"]));
            $pasoverzicht->add("status", new Page\Text($statusPas["Pas_Status-naam"]));
            $pasoverzicht->add("set", new Page\Text($abonnee["Pas-set"]));
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

            $pasoverzicht = new Page\Template("mainAttr/pasbeheer-overzicht");
            $pasoverzicht->add("pastitel", new Page\Text("Bezoekerspas"));
            //$pasoverzicht->add("form", new Page\Text($form));
            $pasoverzicht->add("idabbo", new Page\Text($bezoeker["Pas-idabbo"]));
            $pasoverzicht->add("idsoort", new Page\Text($soortPas2["Soort_Pas-naam"]));
            $pasoverzicht->add("status", new Page\Text($statusPas2["Pas_Status-naam"]));
            $pasoverzicht->add("set", new Page\Text($bezoeker["Pas-set"]));
            $this->template->add("content", $pasoverzicht);
        }
    }

    public function blokkeren(){

        $whereSelect = new Data\Specifier\Where($this->pasModel, new Data\Specifier\WhereCheck('id', '==', $this->user->getPersonID()));
        $this->user->getUsername();
        $result = $this->db->select($this->pasModel,null, $whereSelect);
        if(count($result) == 1){
            $result = $result[0];
        }else {
            return;
        }
        $blokkeren = new Page\Template("mainAttr/pasbeheer-blokkeren");
        $blokkeren->add("aboid", new Page\Text($this->input->get(aboid)));
        $blokkeren->add("set", new Page\Text($this->input->get(set)));
        $this->template->add("content", $blokkeren);

    }

    public function aanvragen(){

        $whereSelect = new Data\Specifier\Where($this->pasModel, new Data\Specifier\WhereCheck('id', '==', $this->user->getPersonID()));
        $this->user->getUsername();
        $result = $this->db->select($this->pasModel, null, $whereSelect);
        if (count($result) == 1) {
            $result = $result[0];
        } else {
            return;
        }
        $aanvragen = new Page\Template("mainAttr/pasbeheer-aanvragen");
        $aanvragen->add("aboid", new Page\Text($this->input->get(aboid)));
        $aanvragen->add("set", new Page\Text($this->input->get(set)));
        $this->template->add("content", $aanvragen);

    }
}
