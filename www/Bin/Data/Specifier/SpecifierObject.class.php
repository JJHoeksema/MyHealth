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
     * Class SpecifierObject
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Data\Specifier
     */
    abstract class SpecifierObject{
        private $model;

        /**
         * Constructor of SpecifierObject
         * @param DataModel $model
         */
        public function __construct(DataModel $model){
            $this->model = $model;
        }

        /**
         * Method to get Model of Specifier
         * @return DataModel
         */
        public function getModel(){
            return $this->model;
        }
    }