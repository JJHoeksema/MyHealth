<?php
namespace Beheer\Controller;

use Beheer\BeheerPermission;
use DMF\Page;
use DMF\Data;

class indexController extends BeheerPermission{
    private $measureModel;
    private $ordersModel;
    private $orderLinesModel;

    public function index(){
        $this->setTitle("Beheer | home");

        $welcome = new Page\Template("beheerAttr/welcome");
        $this->template->add("content", $welcome);

        $welcome->add("name", new Page\Text($this->user->getFirstName()));
    }

    public function measurements() {
        $this->measureModel    =   new Data\FileModel("Measurements");

        $this->setTitle("Beheer | Measurements");
        $measurements = new Page\Template("beheerAttr/measurementsOverzicht");
        $measurements->add("name", new Page\Text($this->user->getFirstName()));

        $selector = new Data\Specifier\Where($this->measureModel, [
            new Data\Specifier\WhereCheck("user_id", "==", $this->user->getID()),
        ]);

        $result = $this->db->select($this->measureModel, null, $selector);
        if(count($result) == 0){
            $measurements->add("content", new Page\Text("Er zijn geen metingen gedaan."));
        }

        foreach($result AS $row) {
            $readings = new Page\Template("/beheerAttr/measurements");
            $readings->add("id", new Page\Text($row['Readings-id']));
            $readings->add("type", new Page\Text($row['Readings-type']));
            $readings->add("naam", new Page\Text($row['Readings-naam']));
            $readings->add("value", new Page\Text($row['Readings-value']));
            $measurements->add("content", $readings);
        }
        $this->template->add("content", $measurements);
    }

    public function ordersMade() {
        $this->ordersModel = new Data\FileModel("Orders");
        $this->orderLinesModel = new Data\FileModel("OrderLine");

        $this->setTitle("Beheer | Facturen");

        $orders = new Page\Template("beheerAttr/facturenOverzicht");

        $orderSelect = new Data\Specifier\Where($this->ordersModel, [
            new Data\Specifier\WhereCheck("UserId", "==", $this->user->getID()),
        ]);

        $orderResult = $this->db->select($this->ordersModel, null, $orderSelect);


        if ($orderResult[0]["Order-Id"] != null) {
            $max = sizeof($orderResult);
            for ($i = 0; $i < $max; $i++ ) {

                $orderLines = new Data\Specifier\Where($this->orderLinesModel, [
                    new Data\Specifier\WhereCheck("OrderId", "==", $orderResult[$i]["Order-Id"])
                ]);
                $orderName = new Page\Template("beheerAttr/orderName");


                $sortedLines = new Data\Specifier\Sort($this->orderLinesModel, 'OrderId', false);

                $orderLinesResult = $this->db->select($this->orderLinesModel, null, [$orderLines, $sortedLines]);

                if ($orderResult[$i]['Order-order_status'] == 0) {
                    $status = "Niet betaald";
                } else {
                    $status = "Betaald";
                }


                foreach ($orderLinesResult AS $row) {
                    $orderLine = new Page\Template("/beheerAttr/orderLine");
                    $orderLine->add("description", new Page\Text($row['OrderLine-Description']));
                    $orderLine->add("code", new Page\Text($row['OrderLine-Code']));
                    $orderLine->add("price", new Page\Text($row['OrderLine-Price']));
                    $orderLine->add("order_status", new Page\Text($row['Order-order_status']));
                    $orderLine->add("status", new Page\Text($status));
                    $orderName->add("content", $orderLine);
                }

                $orderName->add("order_id", new Page\Text($row['OrderLine-OrderId']));
                $orders->add("content" , $orderName);
            }
        } else {
            $orders->add("content", new Page\Text("Er zijn geen facturen beschikbaar."));
        }

        $this->template->add("content", $orders);
    }

}