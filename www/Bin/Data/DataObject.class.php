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
    namespace DMF\Data;

    use DMF\Data\Specifier\SpecifierObject;
    /**
     * Class DataObject
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Data
     */
    abstract class DataObject{
        /**
         * Method to select data from a DataObject
         * @param DataModel $model
         * @param array $columns
         * @param SpecifierObject[]|SpecifierObject $specifierObjects
         * @return array
         */
        public abstract function select(DataModel $model, $columns = null, $specifierObjects = null);

        /**
         * Method to insert data into a DataObject
         * @param DataModel $model
         * @param array $data
         * @return bool
         */
        public abstract function insert(DataModel $model, $data); //TODO: return last inserted row

        /**
         * Method to update data of a DataObject
         * @param DataModel $model
         * @param array $data
         * @param SpecifierObject[]|SpecifierObject $specifierObjects
         * @return bool
         */
        public abstract function update(DataModel $model, $data, $specifierObjects);

        /**
         * Method to delete data out of a DataObject
         * @param DataModel $model
         * @param $specifierObjects
         * @return bool
         */
        public abstract function delete(DataModel $model, $specifierObjects);

        /**
         * Method to validate and covert data to given type
         * @param string $type
         * @param string $value
         * @param bool $convert
         * @return mixed
         */
        protected function validateType($type, $value, $convert = true){
            $modifiedValue = $value;
            //TODO: converting types
            return $modifiedValue;
        }
    }