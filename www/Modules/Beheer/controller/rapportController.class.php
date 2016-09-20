<?php
namespace Beheer\Controller;

use Beheer\BeheerPermission;

use Auth\Account;
use DMF\Events\StatusChangeEvent;
use DMF\Page;
use DMF\Data;
use DateTime;
use BankAPI;

class rapportController extends BeheerPermission
{
    protected $pasModel;
    protected $userModel;
    protected $nawModel;
    protected $aboModel;
    protected $statusModel;
    protected $factuurModel;
    protected $pasgModel;

    public function __construct()
    {
        parent::__construct();
        $this->pasModel = new Data\FileModel("Pas");
        $this->userModel = new Data\FileModel("User");
        $this->aboModel = new Data\FileModel("Abonnement");
        $this->soortModel = new Data\FileModel("Soort_Pas");
        $this->statusModel = new Data\FileModel("Pas_Status");
        $this->factuurModel = new Data\FileModel("Factuur");
        $this->pasgModel =new Data\FileModel("Pas_Gebruik");
        $this->aboid = null;
    }

    public function index()
    {
        $this->setTitle("Pasbeheer | CityPark");
        $maandbedrag = $this->maandTotaal();
        $adbedrag = $this->adbedrag();
        $abobedrag = $maandbedrag - $adbedrag;
        $bezoekuur = $this->bezoekuur();
        $gemiddelde = $this->percentage();
        $rapportages = new Page\Template("beheerAttr/rapportages/rapportages");
        $rapportages->add("maandbedrag", new Page\Text($maandbedrag));
        $rapportages->add("adbedrag", new Page\Text($adbedrag));
        $rapportages->add("abobedrag", new Page\Text($abobedrag));
        $rapportages->add("bezoekuur", new Page\Text($bezoekuur));
        $rapportages->add("gemiddelde", new Page\Text($gemiddelde));
        $this->template->add("content", $rapportages);

    }

    public function maandTotaal(){
        $epoch = time()+60*60;
        $dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
        $maand=$dt->format('m');
        $whereFactuur = new Data\Specifier\Where($this->factuurModel, [
            new Data\Specifier\WhereCheck('maand', '==', $maand),
            new Data\Specifier\WhereCheck('status', '==', 2),
        ]);
        $selectFac = $this->db->select($this->factuurModel, NULL, $whereFactuur);
        $bedrag=0;
        foreach($selectFac AS $row){
            $bedrag+=$row['Factuur-bedrag'];

        }
        return $bedrag;
    }

    public function adbedrag(){
        $wherePas = new Data\Specifier\Where($this->pasModel, [
            new Data\Specifier\WhereCheck('idsoort', '==', 3),
        ]);
        $bedrag=0;
        $selectPas = $this->db->select($this->pasModel, NULL,$wherePas);


        foreach($selectPas AS $row) {
            $whereDatum = new Data\Specifier\Where($this->pasgModel, [
                new Data\Specifier\WhereCheck('idpas', '==', $row["Pas-id"]),
                new Data\Specifier\WhereCheck('betaalstatus', '==', 2),
                new Data\Specifier\WhereCheck('uittijd', "LIKE",date('Y-m-__ %') ),
            ]);
            $selectBedrag = $this->db->select($this->pasgModel, NULL, $whereDatum);
            foreach($selectBedrag AS $ro){
                $bedrag +=$ro["Pas_Gebruik-bedrag"];
            }
        }
        return $bedrag;

    }

    public function  bezoekuur(){
        $wherePas = new Data\Specifier\Where($this->pasModel, [
            new Data\Specifier\WhereCheck('idsoort', '==', 2),
        ]);
        $tijd=0;
        $selectPas = $this->db->select($this->pasModel, NULL,$wherePas);


        foreach($selectPas AS $row) {
            $whereDatum = new Data\Specifier\Where($this->pasgModel, [
                new Data\Specifier\WhereCheck('idpas', '==', $row["Pas-id"]),
                new Data\Specifier\WhereCheck('uittijd', "LIKE",date('Y-m-__ %') ),
            ]);
            $selectBedrag = $this->db->select($this->pasgModel, NULL, $whereDatum);
            foreach($selectBedrag AS $ro){
                $tijd +=$ro["Pas_Gebruik-deltatijd"];
            }
        }
        $tijd = $tijd/60;
        $tijd = round($tijd/60, 2);
        //$tijd = round($tijd, 2);
        return $tijd;
    }

    public function percentage(){
        $whereGebruikMaand = new Data\Specifier\Where($this->pasgModel, [
            new Data\Specifier\WhereCheck('uittijd', "LIKE",date('Y-m-__ %') )
        ]);

        $selectGebruikMaand = $this->db->select($this->pasgModel, NULL, $whereGebruikMaand);
        $totaalMaand = count($selectGebruikMaand);

        $query = "select * from `Pas_Gebruik`
                    where uittijd like '". date('Y-m-__ %')."'
                     group by DATE(uittijd);";
        $array = $this->db->query($query);
        //$perDagGroup = $this->db->query($whereGebruikMaand);
        $perDag = count($array);

        $gemiddelde = round($totaalMaand/$perDag, 2);
        $percentage = ($gemiddelde/300) * 100;
        return round($percentage, 2);

    }
}