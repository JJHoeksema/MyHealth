<?php
/*
 *  DMF: Data Modeler Framework
 *  Copyright (C) 2015  Marwijnn de Kuijper
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
    namespace DMF\Data\Specifier;
    
    use DMF\Data\DataModel;
    /**
     * Class Sort
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Data\Specifier
     */
    class Sort extends SpecifierObject{
        private $columns;
        private $asc;

        /**
         * Constructor of Sort Specifier
         * @param DataModel $model
         * @param $columns
         * @param $asc
         * @throws \Exception
         */
        public function __construct(DataModel $model, $columns, $asc = true){
            parent::__construct($model);

            if(!is_array($columns)) $columns = [$columns];
            foreach($columns as $column){
                if(!$this->getModel()->hasColumn($column)) throw new \Exception("Column '$column' doesn't exist");
            }
            if (count($columns) == 0) throw new \Exception ("Sort Specifier needs at least 1 Column");
            $this->columns = $columns;
            $this->asc = $asc;
        }

        /**
         * Method to get all the columns to sort on (in order)
         * @return array the columns
         */
        public function getColumns(){
            return $this->columns;
        }

        /**
         * Method to get if it is sorted asc
         */
        public function isAsc(){
            return $this->asc;
        }
    }