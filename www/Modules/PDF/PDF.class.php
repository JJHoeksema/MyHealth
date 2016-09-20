<?php

namespace PDF;
use DMF\Data;
include('PDF_invoice.class.php');


class PDF{
 protected $nawModel;
 protected $db;
 protected $facModel;
 protected $aboModel;
    public function __construct()
    {
        $this->db = new Data\MySQLDatabase('localhost', "root", "root");
        $this->nawModel = new Data\FileModel("Naw");
        $this->facModel = new Data\FileModel("Factuur");
        $this->aboModel = new Data\FileModel("Abonnement");
    }
    public function factuur($iduser, $facnummer){
        $whereUser= new Data\Specifier\Where($this->nawModel, [
            new Data\Specifier\WhereCheck('id', '==', $iduser),
        ]);
        $selectUser = $this->db->select($this->nawModel, NULL, $whereUser);

        $whereFac= new Data\Specifier\Where($this->facModel, [
            new Data\Specifier\WhereCheck('id', '==', $facnummer),
        ]);
        $selectFac = $this->db->select($this->facModel, NULL, $whereFac);
        $whereAbo = new Data\Specifier\Where($this->aboModel, [
            new Data\Specifier\WhereCheck('iduser', '==', $iduser),
            new Data\Specifier\WhereCheck('id', '==', $selectFac[0]['Factuur-idabbo']),
        ]);
        $selectAbo= $this->db->select($this->aboModel, NULL, $whereAbo);
        if($selectAbo!=NULL) {

            $pdf = new PDF_Invoice('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->addSociete("Citypark",
                "Grote Vismarkt\n" .
                "7500 XD Gramingen\n" .
                "NL38CPBK0000100000\n"
            );
            if($selectFac[0]['Factuur-status']== 2) {
                $pdf->fact_dev("Betaald", "Betaald");
                $pdf->temporaire("Betaald");
            }

            $pdf->addDate(date("d-m-Y"));
            $pdf->addClient($selectUser[0]['Naw-id']);
            $pdf->addPageNumber("1");
            $pdf->addClientAdresse("" . $selectUser[0]['Naw-land'] . "\n" . $selectUser[0]['Naw-straat'] . ", " . $selectUser[0]['Naw-huisnummer'] . " " . $selectUser[0]['Naw-toevoeging'] . "\n" . $selectUser[0]['Naw-postcode'] . " " . $selectUser[0]['Naw-plaats'] . "");
            $pdf->addReglement("Automatische incasso");
            $pdf->addEcheance($selectFac[0]['Factuur-datumverval']);
            $pdf->addNumTVA($selectFac[0]['Factuur-id']);
            $pdf->addReference("Parkeer-Abonnement");
            $cols = array("REFERENTIE" => 27,
                "BESCHRIJVING" => 90,
                "HOEVEELHEID" => 34,
                "BEDRAG" => 40
            );
            $pdf->addCols($cols);
            $cols = array("REFERENTIE" => "L",
                "BESCHRIJVING" => "L",
                "HOEVEELHEID" => "C",
                "BEDRAG" => "R"
            );
            $pdf->addLineFormat($cols);
            $pdf->addLineFormat($cols);

            $y = 109;
            $line = array("REFERENTIE" => $selectFac[0]['Factuur-idabbo'],
                "BESCHRIJVING" => "Abonnement nummer: " . $selectFac[0]['Factuur-idabbo'] . "\n" . "Maand: " . $selectFac[0]['Factuur-maand'] . "",
                "HOEVEELHEID" => "1",
                "BEDRAG" => $selectFac[0]['Factuur-bedrag']
            );
            $size = $pdf->addLine($y, $line);
            $y += $size + 2;

            $line = array("REFERENTIE" => "REF2",
                "BESCHRIJVING" => "Câble RS232",
                "HOEVEELHEID" => "1",
                "BEDRAG" => "10.00"
            );
            //$size = $pdf->addLine( $y, $line );
            $y += $size + 2;

            //$pdf->addCadreTVAs();

// invoice = array( "px_unit" => value,
//                  "qte"     => qte,
//                  "tva"     => code_tva );
// tab_tva = array( "1"       => 19.6,
//                  "2"       => 5.5, ... );
// params  = array( "RemiseGlobale" => [0|1],
//                      "remise_tva"     => [1|2...],  // {la remise s'applique sur ce code TVA}
//                      "remise"         => value,     // {montant de la remise}
//                      "remise_percent" => percent,   // {pourcentage de remise sur ce montant de TVA}
//                  "FraisPort"     => [0|1],
//                      "portTTC"        => value,     // montant des frais de ports TTC
//                                                     // par defaut la TVA = 19.6 %
//                      "portHT"         => value,     // montant des frais de ports HT
//                      "portTVA"        => tva_value, // valeur de la TVA a appliquer sur le montant HT
//                  "AccompteExige" => [0|1],
//                      "accompte"         => value    // montant de l'acompte (TTC)
//                      "accompte_percent" => percent  // pourcentage d'acompte (TTC)
//                  "Remarque" => "texte"              // texte
            $tot_prods = array(array("px_unit" => 600, "qte" => 1, "tva" => 1),
                array("px_unit" => 10, "qte" => 1, "tva" => 1));
            $tab_tva = array("1" => 19.6,
                "2" => 5.5);
            $params = array("RemiseGlobale" => 1,
                "remise_tva" => 1,       // {la remise s'applique sur ce code TVA}
                "remise" => 0,       // {montant de la remise}
                "remise_percent" => 10,      // {pourcentage de remise sur ce montant de TVA}
                "FraisPort" => 1,
                "portTTC" => 10,      // montant des frais de ports TTC
                // par defaut la TVA = 19.6 %
                "portHT" => 0,       // montant des frais de ports HT
                "portTVA" => 19.6,    // valeur de la TVA a appliquer sur le montant HT
                "AccompteExige" => 1,
                "accompte" => 0,     // montant de l'acompte (TTC)
                "accompte_percent" => 15,    // pourcentage d'acompte (TTC)
                "Remarque" => "Avec un acompte, svp...");

            //$pdf->addTVAs( $params, $tab_tva, $tot_prods);
            //$pdf->addCadreEurosFrancs();
            $pdf->Output();
        }
 }
}
?>