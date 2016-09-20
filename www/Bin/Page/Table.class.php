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
    namespace DMF\Page;

    /**
     * Class Table
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Page
     */
    class Table extends PageObject{
        /**
         * @var array
         */
        private $rows;
        /**
         * @var array|null
         */
        private $header;
        /**
         * @var array
         */
        private $attributes;
        /**
         * @var bool
         */
        private $sanitize;

        /**
         * Constructor table
         * @param array $rows
         * @param null $header
         */
        public function __construct($rows = [], $header = null){
            $this->header = $header;
            $this->rows = $rows;
            $this->attributes = [];
            $this->sanitize = true;
        }

        /**
         * Method to add row
         * @param $row
         */
        public function addRow($row){
            $this->rows[] = $row;
        }

        /**
         * Method to set the header row
         * @param $header
         */
        public function setHeader($header){
            $this->header = $header;
        }

        /**
         * Method to addAttribute
         * @param $name
         * @param $value
         */
        public function addAttribute($name, $value){
            $this->attributes[$name] = $value;
        }

        /**
         * Method to set sanitation
         * @param $bool
         */
        public function setSanitize($bool){
            $this->sanitize = $bool;
        }

        /**
         *  Override render function
         * @return string contents of render
         */
        public function render(){
            $result = '<table';
            foreach($this->attributes as $name => $value){
                $result .= " $name = \"$value\"";
            }
            $result .= '>';
            if($this->header != null){
                $result .= "<tr>";
                foreach($this->header as $column){
                    if($this->sanitize) $column = htmlspecialchars($column);
                    $result .= "<th>$column</th>";
                }
                $result .= "</tr>";
            }
            foreach($this->rows as $row){
                $result .= "<tr>";
                foreach($row as $column){
                    if($this->sanitize) $column = htmlspecialchars($column);
                    $result .= "<td>$column</td>";
                }
                $result .= "</tr>";
            }
            $result .= "</table>";
            return $result;
        }

        /**
         * @return int maximum number of instances  (if 0 no maximum);
         */
        public static function maxInstances(){
            return 0;
        }
    }