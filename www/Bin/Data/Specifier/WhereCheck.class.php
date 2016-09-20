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
    define("DSWC_NOTEQUAL", 0x00);
    define("DSWC_EQUAL",    0x01);
    define("DSWC_GREATER",  0x02);
    define("DSWC_SMALLER",  0x04);
    define("DSWC_NOTLIKE",  0x08);
    define("DSWC_LIKE",     0x09);

    /**
     * Class WhereCheck
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Data\Specifier
     */
    class WhereCheck{
        private $column;
        private $check;
        private $value;

        /**
         * WhereCheck Constructor
         * @param string $column name of column that the specifier checks
         * @param string $check what to check on (==, !=, >, >=, <, <=, !like, like)
         * @param string $value the value to check
         * @throws \Exception if check is invalid
         */
        public function __construct($column, $check, $value){
            $this->column = $column;
            $this->check = $this->checkToInt($check);
            $this->value = $value;
        }
        
        private function checkToInt($check){
            $check = strtolower($check);
            switch($check){
                case "!=":      return DSWC_NOTEQUAL;               // returns 0
                case "==":      return DSWC_EQUAL;                  // returns 1
                case ">":       return DSWC_GREATER;                // returns 2
                case ">=":      return DSWC_GREATER | DSWC_EQUAL;   // returns 3
                case "<":       return DSWC_SMALLER;                // returns 4
                case "<=":      return DSWC_SMALLER | DSWC_EQUAL;   // returns 5
                case "!like":   return DSWC_NOTLIKE;                // returns 8
                case "like":    return DSWC_LIKE;                   // returns 9  DSWC_NOTLIKE | DSWC_EQUAL
                default:        throw new\Exception("Invalid check value"); 
            }
        }

        /**
         * Method to get the name of the column to check
         * @return string column name
         */
        public function getColumn(){
            return $this->column;
        }

        /**
         * Method to get the value to compare with
         * @return string value
         */
        public function getValue(){
            return $this->value;
        }

        /**
         * Method to get an Integer value of the check
         * @return int DSWC_{name}
         */
        public function getCheck(){
            return $this->check;
        }
    }