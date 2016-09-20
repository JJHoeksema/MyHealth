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

    /**
     * Class DataModel
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Data
     */
    abstract class DataModel{
        /**
         * Method to get all DataReferences
         * @return array
         */
        public abstract function getDataReferences();

        /**
         * Method to get all Columns
         * @return array
         */
        public abstract function getColumns();

        /**
         * Method to get the name of the Database
         * @return string|null
         */
        public abstract function getDatabase();

        /**
         * Method to get the name of the table
         * @return string
         */
        public abstract function getTable();

        /**
         * Method to check if the given column exists
         * @param string $columnName column name to search for
         * @return bool
         */
        public function hasColumn($columnName){
            return array_key_exists($columnName, $this->getColumns());
        }
    }