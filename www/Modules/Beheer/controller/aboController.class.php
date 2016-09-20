<?php
    namespace Beheer\Controller;

    use Beheer\BeheerPermission;
    use DMF\Page;
    use DMF\Data;

    class aboController extends BeheerPermission{
        protected $aboModel;
        protected $facModel;
        protected $pasModel;
        public function __construct(){
            parent::__construct();
            $this->aboModel = new Data\FileModel("Abonnement");
            $this->facModel = new Data\FileModel("Factuur");
            $this->pasModel = new Data\FileModel("Pas");
        }
        public function index(){
            $this->setTitle("Beheer | Abonnement");

            $Abo = new Page\Template("beheerAttr/abo/kiezen");

            $this->template->add("content", $Abo);

        }
        public function kiezen(){
            $this->setTitle("Beheer | Abonnement");

            $Abo = new Page\Template("beheerAttr/abo/kiezen2");

            $this->template->add("content", $Abo);

        }

        public function deblock(){

            $abo = $this->input->get('id');
            $whereAbo = new Data\Specifier\Where($this->aboModel, new Data\Specifier\WhereCheck("id", "==", "$abo"));
            $selectAbo= $this->db->select($this->aboModel, NULL, $whereAbo);
            if($selectAbo!=NULL) {

                $abodata = [
                    'status' => 1
                ];
                $this->db->update($this->aboModel, $abodata, $whereAbo);
                $wherefactuur = new Data\Specifier\Where($this->facModel,[
                    new Data\Specifier\WhereCheck("idabbo", "==", "$abo"),
                    new Data\Specifier\WhereCheck("id", "==","3")
                ]);
                $facdata = [
                    'status' => 2
                ];
                $this->db->update($this->facModel, $facdata, $wherefactuur);
                $wherePas = new Data\Specifier\Where($this->pasModel, new Data\Specifier\WhereCheck("idabbo", "==", "$abo"));
                $selectPas = $this->db->select($this->pasModel, NULL, $wherePas);
                $value = 0;
                foreach ($selectPas AS $row) {
                    if ($row['Pas-set'] > $value) {
                        $value = $row['Pas-set'];
                    }
                }
                $wherePas2 = new Data\Specifier\Where($this->pasModel, [
                    new Data\Specifier\WhereCheck("idabbo", "==", "$abo"),
                    new Data\Specifier\WhereCheck("set", "==", "$value")
                ]);
                $pasData = [
                    'status' => 1
                ];
                $this->db->update($this->pasModel, $pasData, $wherePas2);
            }
            $this->request->redirect("beheer/abo/");
        }
        public function block(){

            $abo = $this->input->get('id');

            $whereAbo = new Data\Specifier\Where($this->aboModel, new Data\Specifier\WhereCheck("id", "==", "$abo"));
            $selectAbo= $this->db->select($this->aboModel, NULL, $whereAbo);
            if($selectAbo!=NULL) {

                $abodata = [
                    'status' => 2
                ];
                $this->db->update($this->aboModel, $abodata, $whereAbo);
                $wherefactuur = new Data\Specifier\Where($this->facModel, [
                    new Data\Specifier\WhereCheck("idabbo", "==", "$abo"),
                    new Data\Specifier\WhereCheck("id", "==","1")
                ]);
                $facdata = [
                    'status' => 3
                ];
                $this->db->update($this->facModel, $facdata, $wherefactuur);
                $wherePas = new Data\Specifier\Where($this->pasModel, new Data\Specifier\WhereCheck("idabbo", "==", "$abo"));
                $selectPas = $this->db->select($this->pasModel, NULL, $wherePas);
                $value = 0;
                foreach ($selectPas AS $row) {
                    if ($row['Pas-set'] > $value) {
                        $value = $row['Pas-set'];
                    }
                }
                $wherePas2 = new Data\Specifier\Where($this->pasModel, [
                    new Data\Specifier\WhereCheck("idabbo", "==", "$abo"),
                    new Data\Specifier\WhereCheck("set", "==", "$value")
                ]);
                $pasData = [
                    'status' => 2
                ];
                $this->db->update($this->pasModel, $pasData, $wherePas2);
            }
            $this->request->redirect("beheer/abo/");
        }

    }